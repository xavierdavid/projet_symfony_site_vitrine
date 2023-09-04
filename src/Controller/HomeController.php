<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\MetatagRepository;
use App\Form\NewsletterRegistrationType;
use App\Services\SubscribeToMailJet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    /**
     * Contrôle l'affichage de la page 'Acueil' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository, ArticleRepository $articleRepository, Request $request, SubscribeToMailJet $subscribeToMailJet): Response
    {
        // Récupération de l'objet Metatag de la page 'Accueil'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Accueil'
        ]);
        // Récupération des objets Article à la une triés par ordre priorité et limité à 3 articles
        $articles = $articleRepository->findBy(['isFrontPage' => true,],['priorityOrder' => 'ASC'],3);
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
            return $this->redirectToRoute('app_home');
        }
        $formView = $form->createView();
        return $this->render('home/index.html.twig', [
            'metatag' => $metatag,
            'articles' => $articles,
            'formView'=> $formView
        ]);
    }
}
