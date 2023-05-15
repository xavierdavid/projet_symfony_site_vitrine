<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Services\SendEmail;
use App\Form\UpdateEmailType;
use App\Form\UpdatePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\throwException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        // Récupération de l'objet User correspondant au resetToken
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

    #[Route('/account/update/password', name:'app_account_update_password')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification du mot de passe de l'utilisateur
     *
     * @param Request $request
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        // Récupération de l'objet User authentifié
        $user = $this->getUser();
        // Création du formulaire de modification du mot de passe de l'utilisateur
        $form = $this->createForm(UpdatePasswordType::class, $user);
        // Récupération des données du formulaire via la requête
        $form->handleRequest($request);
        // Vérification des données (soumission et validité)
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'ancien mot de passe saisi par l'utilisateur dans le formulaire
            $oldPassword = $form->get('old_password')->getData();
            // Vérification si le mot de passe actuel saisi par l'utilisateur dans le formulaire est identique au mot de passe haché stocké dans la base de données
            if($userPasswordHasherInterface->isPasswordValid($user, $oldPassword)) {
                // Récupération du nouveau mot de passe saisi
                $newPassword = $form->get('new_password')->getData();
                // Hachage du nouveau mot de passe
                $password = $userPasswordHasherInterface->hashPassword($user, $newPassword);
                // Affectation du nouveau mot de passe haché à l'objet User
                $user->setPassword($password);
                // Envoi en base de données
                $this->entityManagerInterface->persist($user);
                $this->entityManagerInterface->flush();
                // Message flash et redirection
                $this->addFlash("success", "Votre mot de passe a été modifié avec succès !");
                return $this->redirectToRoute(('app_account_home'));
            } else {
                // Message flash et redirection en cas d'échec
                $this->addFlash("danger", "Désolé, aucun utilisateur n'existe avec ce mot de passe !");
                return $this->redirectToRoute(('app_account_update_password'));
            }
        }
        $formView = $form->createView();
        return $this->render('/account/update_password.html.twig', [
            'formView' => $form,
            'user' => $user
        ]);
    }

    #[Route('/account/update/{id}/profile', name:'app_account_update_profile')]
    public function updateProfile($id, Request $request)
    {
        // Récupération de l'objet User authentifié
        $user = $this->getUser();
        // Vérification de l'existence de l'objet User à modifier
        if(!$user) {
            throw $this->createNotFoundException("L'utilisateur demandé n'existe pas !");
        }
        // Construction du formulaire de modification de profil de l'objet User
        $form = $this->createForm(ProfileType::class, $user);
        // Analyse de la requête
        $form->handlerequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()){
            // Récupération du prénom saisi via le formulaire
            $firstname = $form->get('firstname')->getData();
            // Affectation du prénom à l'objet User avec la première lettre en majuscule
            $user->setFirstname(ucfirst($firstname));
            // Récupération du nom saisi via le formulaire
            $lastname = $form->get('lastname') ->getData();
            // Affectation du nom à l'objet User avec la première lettre en majuscule
            $user->setLastname(strtoupper($lastname));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($user);
            // Message flash et redirection
            $this->addFlash("success", "Les informations de votre profil ont été modifiées avec succès !");
            return $this->redirectToRoute('app_account_home');
        }
        $formView = $form->createView();
        return $this->render('/account/update_profile.html.twig', [
            'formView' => $formView,
            'user' => $user
        ]);
    }
}
