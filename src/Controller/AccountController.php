<?php

namespace App\Controller;

use App\Services\SendEmail;
use App\Form\UpdateEmailType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{
    private $sendEmail;
    private $entityManagerInterface;

    public function __construct(SendEmail $sendEmail, EntityManagerInterface $entityManagerInterface)
    {
        $this->sendEmail = $sendEmail;
        $this->entityManagerInterface = $entityManagerInterface;
    }
    #[Route('/account', name: 'app_account_home')]
    /**
     * Contrôle l'affichage de la page d'accueil du compte de l'utilisateur authentifié
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            
        ]);
    }

    #[Route('/account/update/email', name:'app_account_update_email')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification de l'email de l'utilisateur
     *
     * @param Request $request
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     * @param UserRepository $userRepository
     * @return Response
     */
    public function updateEmail(Request $request, TokenGeneratorInterface $tokenGeneratorInterface): Response
    {
        // Récupération de l'objet User authentifié
        $user = $this->getUser();
        // Création du formulaire de modification de l'email
        $form = $this->createForm(UpdateEmailType::class);
        // Récupération des données du formulaire via la requête
        $form->handleRequest($request);
        // Vérification des données (soumission et validité)
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération du nouvel email saisi via le formulaire
            $newEmail = $form->get('new_email')->getData();
            // Création d'un token de réinitialisation
            $resetToken = $tokenGeneratorInterface->generateToken();
            // Envoi du token de réinitialisation et du nouvel email en base de données
            try {
                $user->setResetToken($resetToken);
                $user->setTemporaryEmail($newEmail);
                $this->entityManagerInterface->persist($user);
                $this->entityManagerInterface->flush();
            } catch (\Exception $e) {
                // En cas d'échec, on envoie un message flash
                $this->addFlash("danger","Une erreur est survenue : ". $e->getMessage());
                // Redirection vers la page du compte del'utilisateur
                return $this->redirectToRoute('app_account_home');
            }
            // Paramétrage de l'email de demande de modification de l'adresse email
            $from = "contact@xavier-david.com";
            $to = $newEmail;
            $subject = "Vérification de votre nouvelle adresse email";
            $htmlTemplate = "email/update_email.html.twig";
            $context = [
                'user' => $user,
                'resetToken' => $resetToken
            ];
            // Envoi de l'email de de mande de modification
            try {
                $this->sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
                // Envoi d'un message flash
                $this->addFlash("success", "Un lien vient de vous être envoyé à l'adresse " . $newEmail. " pour vérifier votre adresse email.");
                // Rédirection vers la page du compte de l'utilisateur
                return $this->redirectToRoute('app_account_home');
            } catch (TransportExceptionInterface $e) {
                // Envoi d'un message flash
                $this->addFlash("danger", "Un problème est survenu, veuillez contacter l'administrateur du site.");
                // Rédirection vers la page du compte de l'utilisateur
                return $this->redirectToRoute('app_account_home');
            }
        }
        $formView = $form->createView();
        return $this->render('/account/update_email.html.twig', [
            'formView' => $formView,
            'user' => $user
        ]);
    }

    #[Route('/account/reset/email/{resetToken}', name:'app_account_reset_email')]
    /**
     * Contrôle la procédure de réinitialisation de l'email associé au compte de l'utilisateur
     *
     * @param [type] $resetToken
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function resetEmail($resetToken, Request $request, UserRepository $userRepository): Response
    {
        // Récupération de l'utilisateur correspondant au resetToken
        $user = $userRepository->findOneBy(['resetToken'=>$resetToken]);
        // Si aucun utilisateur n'existe avec ce token
        if(!$user) {
            // Envoi d'un message flash
            $this->addFlash("danger", "Aucun utilisateur reconnu avec ce token de réinitialisation !");
            // Redirection vers la page du compte de l'utilisateur
            return $this->redirectToRoute('app_account_home');
        }
        // Récupération du nouvel email stocké temporairement en base de données
        $newEmail = $user->getTemporaryEmail();
        // Suppression du token de réinitialisation
        $user->setResetToken(null);
        // Affectation du nouvel email à la propriété 'email' de l'objet User
        $user->setEmail($newEmail);
        // Suppression de l'email temporaire
        $user->setTemporaryEmail(null);
        // Envoi en base de données
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
        // Message Flash et redirection
        $this->addFlash("success", "L'adresse email associée à votre compte a été modifiée avec succès !");
        return $this->redirectToRoute('app_account_home');
    }
}
