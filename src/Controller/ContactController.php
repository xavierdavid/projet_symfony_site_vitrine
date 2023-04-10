<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Services\SendEmail;
use App\Repository\MetatagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de la page 'Contact' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(Request $request, MetatagRepository $metatagRepository, EntityManagerInterface $entityManagerInterface, SendEmail $sendEmail): Response
    {
        // Récupération de l'objet Metatag de la page 'Contact'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Contact'
        ]);
        // Création d'une nouvelle instance de la classe Contact
        $contact = new Contact;
        // Construction du formulaire de création d'un objet Contact
        $form = $this->createForm(ContactType::class, $contact);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de l'objet Contact en base de données
            $entityManagerInterface->persist($contact);
            $entityManagerInterface->flush();
            // Paramétrage de l'email de contact
            $from = $contact->getEmail();
            $to = "xav.david28@gmail.com";
            $subject = $contact->getSubject();
            $htmlTemplate = "email/contact.html.twig";
            $context = ['contact' => $contact];
            // Envoi de l'email
            try {
                $sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
                // Message flash et redirection
                $this->addFlash('success', 'Votre message a bien été envoyé !');
                $this->redirectToRoute('app_contact');
            } catch (TransportExceptionInterface $e) {
                // Message d'erreur
                $this->addFlash('warning', 'Un problème est survenu, veuillez contacter l\'administrateur du site');
                // Redirection
                return $this->redirectToRoute('app_contact');
            }
        }
        $formView = $form->createView();
        return $this->render('contact/index.html.twig', [
            'formView' => $formView,
            'metatag' => $metatag
        ]);
    }
}
