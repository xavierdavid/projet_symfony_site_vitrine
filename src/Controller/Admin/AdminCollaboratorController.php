<?php

namespace App\Controller\Admin;

use App\Entity\Collaborator;
use App\Form\CollaboratorType;
use App\Services\UploadFile;
use App\Form\SearchCollaboratorType;
use App\Services\SearchCollaborator;
use App\Repository\CollaboratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCollaboratorController extends AbstractController
{
    private $entityManagerInterface;
    private $sluggerInterface;
    private $uploadFile;

    public function __construct(EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface, UploadFile $uploadFile)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->sluggerInterface = $sluggerInterface;
        $this->uploadFile = $uploadFile;
    }
    
    #[Route('/admin/collaborator/new', name:'app_admin_collaborator_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Collaborator
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        // Création d'une nouvelle instance de la classe Collaborator
        $collaborator = new Collaborator;
        // Construction du formulaire de création d'un objet Collaborator
        $form = $this->createForm(CollaboratorType::class, $collaborator);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel fichier image de couverture de l'objet Collaborator envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Si un fichier image a bien été transmis
            if($coverImage) {
                // Upload du nouveau fichier d'image de couverture de l'objet Collaborator
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Collaborator
                $collaborator->setCoverImage($newCoverImage);
            }
            // Mise à jour de la propriété slug de l'objet Collaborator
            $collaborator->setSlug(strtolower($this->sluggerInterface->slug($collaborator->getfirstName().''.$collaborator->getLastname())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($collaborator);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le collaborateur a été créé avec succès !");
            return $this->redirectToRoute('app_admin_collaborator_index');
        }
        $formView = $form->createView();
        return $this->render('admin/collaborator/new.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/collaborator/index', name: 'app_admin_collaborator_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Collaborator
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @param CollaboratorRepository $collaboratorRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, CollaboratorRepository $collaboratorRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Collaborator
        $searchCollaborator = new SearchCollaborator;
        // Création du formulaire de recherche d'objets Collaborator
        $form = $this->createForm(SearchCollaboratorType::class, $searchCollaborator);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Collaborator sélectionnés à l'aide des propriétés de l'objet SearchCollaborator
        $collaboratorsData = $collaboratorRepository->findWithSearchCollaborator($searchCollaborator);
        // Pagination des objets Collaborator
        $collaborators = $paginatorInterface->paginate(
            // Objets Collaborator récupérés
            $collaboratorsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Collaborator à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/collaborator/index.html.twig', [
            'collaborators' => $collaborators,
            'collaboratorsData' => $collaboratorsData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/collaborator/{slug}/edit', name:'app_admin_collaborator_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Collaborator
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $slug
     * @param Request $request
     * @param CollaboratorRepository $collaboratorRepository
     * @return Response
     */
    public function edit($slug, Request $request, CollaboratorRepository $collaboratorRepository): Response
    {
        // Récupération de l'objet Collaborator à modifier
        $collaborator = $collaboratorRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Collaborator à modifier
        if(!$collaborator) {
            throw $this->createNotFoundException("Le collaborateur demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Collaborator
        $form = $this->createForm(CollaboratorType::class, $collaborator);
        // Récupération de l'ancien fichier d'image de couverture de l'objet Collaborator
        $oldCoverImage = $collaborator->getCoverImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image de couverture de l'objet Collaborator envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image de couverture de l'objet Collaborator transmis par le formulaire
            if($coverImage) {
                // Si un ancien fichier d'image de couverture l'objet Collaborator existe
                if($oldCoverImage) {
                    // Suppression de l'ancien fichier d'image de couverture l'objet Collaborator dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldCoverImage);
                }
                // Upload du nouveau fichier d'image de couverture de l'objet Collaborator
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Collaborator
                $collaborator->setCoverImage($newCoverImage);
            } else {
                // Si aucun nouveau fichier d'image de couverture de l'objet Collaborator n'a été transmis, on conserve l'ancien
                $collaborator->setCoverImage($oldCoverImage);
            }
            // Affectation du slug à l'objet Collaborator
            $collaborator->setSlug(strtolower($this->sluggerInterface->slug($collaborator->getfirstName().''.$collaborator->getlastName())));
            // Affectation de la date de mise à jour à l'objet Collaborator
            $collaborator->setUpdatedAt(new \Datetime);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($collaborator);
            // Message flash et redirection
            $this->addFlash("success", "Le collaborateur a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_collaborator_index');
        }
        $formView = $form->createView();
        return $this->render('admin/collaborator/edit.html.twig', [
            'formView' => $formView,
            'collaborator' => $collaborator
        ]);
    }

    #[Route('/admin/collaborator/{slug}/detail', name:'app_admin_collaborator_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Collaborator
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $slug
     * @param Request $request
     * @param CollaboratorRepository $collaboratorRepository
     * @return Response
     */
    public function detail($slug, CollaboratorRepository $collaboratorRepository): Response
    {
        // Récupération de l'objet Collaborator à afficher
        $collaborator = $collaboratorRepository->findOneBy([
            'slug' => $slug
        ]);
        return $this->render('/admin/collaborator/detail.html.twig', [
            'collaborator' => $collaborator
        ]);
    }

    #[Route('/admin/collaborator/{slug}/delete', name:'app_admin_collaborator_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Collaborator
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $slug
     * @param CollaboratorRepository $collaboratorRepository
     * @return Response
     */
    public function delete($slug, Request $request, CollaboratorRepository $collaboratorRepository): Response
    {
        // Récupération de l'objet Collaborator à supprimer
        $collaborator = $collaboratorRepository->findOneBy([
            'slug' =>$slug
        ]);
        // Récupération du fichier d'image de couverture de l'objet Collaborator à supprimer
        $coverImage = $collaborator->getCoverImage();
        // Vérification de l'existence de l'objet Collaborator à supprimer
        if(!$collaborator){
            throw $this->createNotFoundException("Le collaborateur demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Collaborator
        if($this->isCsrfTokenValid('delete'.$collaborator->getSlug(), $request->get('_token'))){
            // Suppression de l'objet Collaborator
            $this->entityManagerInterface->remove($collaborator);
            // Si l'objet Collaborator à supprimer possède un fichier d'image de couverture
            if($coverImage) {
                // Suppression du fichier d'image de couverture de l'objet Collaborator dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$coverImage);
            }
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($collaborator);
            // Message flash et redirection
            $this->addFlash("success","Le collaborateur a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_collaborator_index');
        }
    }
}
