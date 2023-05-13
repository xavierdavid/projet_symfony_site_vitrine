<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Form\SearchUserType;
use App\Services\SearchUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserController extends AbstractController
{
    private $entityManagerInterface;
    private $userPasswordHasherInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface) {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    #[Route('/admin/user/new', name:'app_admin_user_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet User ayant le rôle 'AUTHOR'
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        // Création d'une nouvelle instance de la classe User
        $user = new User;
        // Construction du formulaire de création d'un objet User
        $form = $this->createForm(UserType::class, $user);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Affectation du prénom à l'objet User avec la première lettre en majuscule
            $user->setFirstName(ucfirst($user->getFirstName()));
            // Affectation du nom à l'objet User en capitales
            $user->setLastName(strtoupper($user->getLastName()));
            // Récupération du mot de passe saisi
            $password = $form->get('password')->getData();
            // Hashage du mot de passe
            $hashPassword = $this->userPasswordHasherInterface->hashPassword($user, $password);
            // Affectation du mot de passe hasché à l'objet User 
            $user->setPassword($hashPassword);
            // Affectation du rôle 'AUTHOR' 
            $user->setRoles(['ROLE_AUTHOR']);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "L'utilisateur a été créée avec succès !");
            return $this->redirectToRoute('app_admin_user_index');
        }
        $formView = $form->createView();
        return $this->render('admin/user/new.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/user/index', name:'app_admin_user_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets User
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @param PaginatorInterface $paginatorInterface
     * @param userRepository $userRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, UserRepository $userRepository): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets User
        $searchUser = new SearchUser;
        // Création du formulaire de recherche d'objets User
        $form = $this->createForm(SearchUserType::class, $searchUser);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets user sélectionnés à l'aide des propriétés de l'objet SearchUser
        $usersData = $userRepository->findWithSearchUser($searchUser);
       
        // Pagination des objets User
        $users = $paginatorInterface->paginate(
            // Objets User récupérés
            $usersData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets user à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'usersData' => $usersData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/user/{id}/edit', name:'app_admin_user_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet User
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param Request $request
     * @return Response
     */
    public function edit($id, Request $request, UserRepository $userRepository): Response
    {
        // Récupération de l'objet User à modifier
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet User à modifier
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur demandé n'existe pas !");
        }
        // Récupération du tableau des rôles de l'objet User à modifier
        $roles = $user->getRoles();
        // Si l'objet User à modifier a le rôle d'administrateur
        if(in_array("ROLE_ADMIN", $roles)) {
            // Redirection vers la page de profil
            return $this->redirectToRoute('app_account_home');
        }
        // Construction du formulaire de modification de l'objet User
        $form = $this->createForm(UserType::class, $user);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Affectation du prénom à l'objet User avec la première lettre en majuscule
            $user->setFirstName(ucfirst(strtolower($user->getFirstName())));
            // Affectation du nom à l'objet User en capitales
            $user->setLastName(strtoupper($user->getLastName()));
            // Récupération du mot de passe saisi
            $password = $form->get('password')->getData();
            // Hashage du mot de passe
            $hashPassword = $this->userPasswordHasherInterface->hashPassword($user, $password);
            // Affectation du mot de passe hasché à l'objet User 
            $user->setPassword($hashPassword);
            // Affectation du rôle 'AUTHOR' 
            $user->setRoles(['ROLE_AUTHOR']);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "L'utilisateur a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_user_index');
        }
        $formView = $form->createView();
        return $this->render('admin/user/edit.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/user/{id}/delete', name:'app_admin_user_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet User
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param [type] $id
     * @param UserRepository $userRepository
     * @return Void
     */
    public function delete($id, Request $request, UserRepository $userRepository)
    {
        // Récupération de l'objet User à supprimer
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet User à supprimer
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur demandé n'existe pas !");
        }
        // Récupération des objets Article associés à l'objet User
        $articles = $user->getArticles();
        // Vérification de l'existence d'objets Article associés à l'objet user
        if($articles->isEmpty()) {
            // Test du token autorisant la suppression de l'objet User
            if($this->isCsrfTokenValid('delete'.$user->getid(), $request->get('_token'))){
                // Suppression de l'objet User
                $this->entityManagerInterface->remove($user);
                // Enregistrement en base de données
                $this->entityManagerInterface->flush($user);
                // Message flash et redirection
                $this->addFlash("success","L'utilisateur a été supprimé avec succès !");
                return $this->redirectToRoute('app_admin_user_index');
            }  
        } 
        // Message flash et redirection
        $this->addFlash("warning", "Impossible de supprimer l'utilisateur car ce dernier est associé à un ou plusieurs articles !");
        return $this->redirectToRoute('app_admin_user_index');
    }
}
