<?php

namespace App\Controller;

use App\Services\SubscribeToMailJet;
use App\Form\NewsletterRegistrationType;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FooterController extends AbstractController
{
    #[Route('/newsletter/registration', name:'app_newsletter_registration')]
    /**
     * Contrôle l'affichage et le traitement du formulaire d'inscription à la newsletter dans le footer du site
     *
     * @param OrganizationRepository $organizationRepository
     * @param Request $request
     * @param SubscribeToMailJet $subscribeToMailJet
     * @return Response
     */
    public function index(OrganizationRepository $organizationRepository, Request $request, SubscribeToMailJet $subscribeToMailJet): Response
    {   
        // Récupération du dernier objet Organization inséré en base de données
        $organization = $organizationRepository->findBy([], [
            'id' => 'DESC'], // Tri par identifiant et par ordre décroissant
            1, // Limite de 1 enregistrement
            0 // Offset
        );
        // Création du formulaire d'inscription à la newsletter
        $form = $this->createForm(NewsletterRegistrationType::class);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'email saisi
            $email = $form->get('email')->getData();
            // Création d'un nouveau contact 'email' sur le compte MailJet de l'administrateur
            $subscribeToMailJet->addNewContact($email);
            // Message flash et redirection
            $this->addFlash('success', 'Votre inscription à la newsletter a bien été prise en compte.');
            // redirection vers la dernière route
            $lastRoute = $request->headers->get('referer');
            return $this->redirect($lastRoute);
        }
        $formView = $form->createView();
        return $this->render('partials/_footer.html.twig', [
            'formView' => $formView,
            'organization'=> $organization
        ]);
    }
}