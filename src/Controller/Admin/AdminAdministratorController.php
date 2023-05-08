<?php

namespace App\Controller\Admin;

use App\Entity\Administrator;
use App\Form\AdministratorType;
use App\Services\UploadFile;
use App\Form\SearchAdministratorType;
use App\Services\SearchAdministrator;
use App\Repository\AdministratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdministratorController extends AbstractController
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
    
    #[Route('/admin/administrator/new', name:'app_admin_administrator_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Administrator
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        // Création d'une nouvelle instance de la classe Administrator
        $administrator = new Administrator;
        // Construction du formulaire de création d'un objet Administrator
        $form = $this->createForm(AdministratorType::class, $administrator);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel fichier image de couverture de l'objet Administrator envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Si un fichier image a bien été transmis
            if($coverImage) {
                // Upload du nouveau fichier d'image de couverture de l'objet Administrator
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Administrator
                $administrator->setCoverImage($newCoverImage);
            }
            // Mise à jour de la propriété slug de l'objet Administrator
            $administrator->setSlug(strtolower($this->sluggerInterface->slug($administrator->getfirstName().''.$administrator->getLastname())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($administrator);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le dirigeant a été créé avec succès !");
            return $this->redirectToRoute('app_admin_administrator_index');
        }
        $formView = $form->createView();
        return $this->render('admin/administrator/new.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/administrator/index', name: 'app_admin_administrator_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Administrator
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param AdministratorRepository $administratorRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, AdministratorRepository $administratorRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Administrator
        $searchAdministrator = new SearchAdministrator;
        // Création du formulaire de recherche d'objets Administrator
        $form = $this->createForm(SearchAdministratorType::class, $searchAdministrator);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Administrator sélectionnés à l'aide des propriétés de l'objet SearchAdministrator
        $administratorsData = $administratorRepository->findWithSearchAdministrator($searchAdministrator);
        // Pagination des objets Administrator
        $administrators = $paginatorInterface->paginate(
            // Objets Administrator récupérés
            $administratorsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Administrator à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/administrator/index.html.twig', [
            'administrators' => $administrators,
            'administratorsData' => $administratorsData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/administrator/{slug}/edit', name:'app_admin_administrator_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Administrator
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param AdministratorRepository $administratorRepository
     * @return Response
     */
    public function edit($slug, Request $request, AdministratorRepository $administratorRepository): Response
    {
        // Récupération de l'objet Administrator à modifier
        $administrator = $administratorRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Administrator à modifier
        if(!$administrator) {
            throw $this->createNotFoundException("Le dirigeant demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Administrator
        $form = $this->createForm(AdministratorType::class, $administrator);
        // Récupération de l'ancien fichier d'image de couverture de l'objet Administrator
        $oldCoverImage = $administrator->getCoverImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image de couverture de l'objet Administrator envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image de couverture de l'objet Administrator transmis par le formulaire
            if($coverImage) {
                // Si un ancien fichier d'image de couverture l'objet Administrator existe
                if($oldCoverImage) {
                    // Suppression de l'ancien fichier d'image de couverture l'objet Administrator dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldCoverImage);
                }
                // Upload du nouveau fichier d'image de couverture de l'objet Administrator
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Administrator
                $administrator->setCoverImage($newCoverImage);
            } else {
                // Si aucun nouveau fichier d'image de couverture de l'objet Administrator n'a été transmis, on conserve l'ancien
                $administrator->setCoverImage($oldCoverImage);
            }
            // Affectation du slug à l'objet Administrator
            $administrator->setSlug(strtolower($this->sluggerInterface->slug($administrator->getfirstName().''.$administrator->getlastName())));
            // Affectation de la date de mise à jour à l'objet Administrator
            $administrator->setUpdatedAt(new \Datetime);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($administrator);
            // Message flash et redirection
            $this->addFlash("success", "Le dirigeant a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_administrator_index');
        }
        $formView = $form->createView();
        return $this->render('admin/administrator/edit.html.twig', [
            'formView' => $formView,
            'administrator' => $administrator
        ]);
    }

    #[Route('/admin/administrator/{slug}/detail', name:'app_admin_administrator_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Administrator
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param AdministratorRepository $administratorRepository
     * @return Response
     */
    public function detail($slug, AdministratorRepository $administratorRepository): Response
    {
        // Récupération de l'objet Administrator à afficher
        $administrator = $administratorRepository->findOneBy([
            'slug' => $slug
        ]);
        return $this->render('/admin/administrator/detail.html.twig', [
            'administrator' => $administrator
        ]);
    }

    #[Route('/admin/administrator/{slug}/delete', name:'app_admin_administrator_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Administrator
     *
     * @param [type] $slug
     * @param AdministratorRepository $administratorRepository
     * @return Response
     */
    public function delete($slug, Request $request, AdministratorRepository $administratorRepository): Response
    {
        // Récupération de l'objet Administrator à supprimer
        $administrator = $administratorRepository->findOneBy([
            'slug' =>$slug
        ]);
        // Récupération du fichier d'image de couverture de l'objet Administrator à supprimer
        $coverImage = $administrator->getCoverImage();
        // Vérification de l'existence de l'objet Administrator à supprimer
        if(!$administrator){
            throw $this->createNotFoundException("Le dirigeant demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Administrator
        if($this->isCsrfTokenValid('delete'.$administrator->getSlug(), $request->get('_token'))){
            // Suppression de l'objet Administrator
            $this->entityManagerInterface->remove($administrator);
            // Si l'objet Administrator à supprimer possède un fichier d'image de couverture
            if($coverImage) {
                // Suppression du fichier d'image de couverture de l'objet Administrator dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$coverImage);
            }
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($administrator);
            // Message flash et redirection
            $this->addFlash("success","Le dirigeant a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_administrator_index');
        }
    }
}
