<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Services\UploadFile;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDocumentController extends AbstractController
{
    private $entityManagerInterface;
    private $sluggerInterface;
    private $uploadFile;

    public function __construct(EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface, UploadFile $uploadFile)
    {
        $this->entityManagerInterface=$entityManagerInterface;
        $this->sluggerInterface=$sluggerInterface;
        $this->uploadFile = $uploadFile;
    }

    #[Route('/admin/document/new', name:'app_admin_document_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Document
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        // Création d'une nouvelle instance de la classe Document
        $document = new Document;
        // Construction du formulaire de création d'un objet Document
        $form = $this->createForm(DocumentType::class, $document);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier de l'objet Document envoyé par le formulaire
            $documentFile = $form->get('documentFile')->getData();
            // Upload du fichier de l'objet Document
            $newDocumentFile = $this->uploadFile->upload($documentFile);
            // Affectation des valeurs aux propriétés de l'objet Document
            $document->setSlug(strtolower($this->sluggerInterface->slug($document->getName())));
            $document->setDocumentFile($newDocumentFile);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($document);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le document a été créé avec succès !");
            return $this->redirectToRoute('app_admin_document_index');
        }
        $formView = $form->createView();
        return $this->render('admin/document/new.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/document/index', name:'app_admin_document_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Document
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, DocumentRepository $documentRepository): Response
    {
        // Récupération des objets Document en base de données
        $documentsData = $documentRepository->findBy([],[
            'name' => 'ASC',
        ]);
        // Pagination des objets Document
        $documents = $paginatorInterface->paginate(
            // Objets Document récupérés
            $documentsData, 
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page',1),
            // Nombre d'objets Document à afficher par page
            10
        );
        return $this->render('/admin/document/index.html.twig', [
            'documents' => $documents,
            'documentsData' => $documentsData
        ]);
    }

    #[Route('/admin/document/{slug}/edit', name:'app_admin_document_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Document
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $slug
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function edit($slug, Request $request, DocumentRepository $documentRepository): Response
    {
        // Récupération de l'objet Document à modifier
        $document = $documentRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Document à modifier
        if(!$document){
            throw $this->createNotFoundException("Le document demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Document
        $form = $this->createForm(DocumentType::class, $document);
        // Récupération de l'ancien fichier de l'objet Document
        $oldDocumentFile = $document->getDocumentFile();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier de l'objet Document envoyé par le formulaire
            $documentFile = $form->get('documentFile')->getData();
            // Vérification de l'existence d'un nouveau fichier de l'objet Document transmis par le formulaire
            if($documentFile) {
                // Si un ancien fichier de l'objet Document existe
                if($oldDocumentFile) {
                    // Suppression de l'ancien fichier de l'objet Document dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldDocumentFile);
                }
                // Upload du nouveau fichier de l'objet Document
                $newDocumentFile = $this->uploadFile->upload($documentFile);
                // Mise à jour de la propriété documentFile de l'objet Document
                $document->setDocumentFile($newDocumentFile);
            } else {
                // Si aucun nouveau fichier de l'objet Document n'a été transmis, on conserve l'ancien
                $document->setDocumentFile($oldDocumentFile);
            }
            // Affectation du slug à l'objet Document
            $document->setSlug(strtolower($this->sluggerInterface->slug($document->getName())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($document);
            // Message flash et redirection
            $this->addFlash("success", "Le document a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_document_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/document/edit.html.twig', [
            'formView' => $formView,
            'document' => $document
        ]);  
    }

    #[Route('/admin/document/{slug}/delete', name:'app_admin_document_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Document
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $slug
     * @param DocumentRepository $documentRepository
     * @return Void
     */
    public function delete($slug, Request $request, DocumentRepository $documentRepository)
    {
        // Récupération de l'objet Document à supprimer
        $document = $documentRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Document à supprimer
        if(!$document){
            throw $this->createNotFoundException("Le document demandé n'existe pas !");
        }
        // Récupération des objets Article associés à l'objet Document
        $articles = $document->getArticles();
        // Récupération des objets Product associés à l'objet Document
        $products = $document->getProducts();
        // Vérification de l'existence d'objets Article ou Product associés à l'objet Image
        if($articles->isEmpty() && $products->isEmpty()) {
            // Test du token autorisant la suppression de l'objet Document
            if($this->isCsrfTokenValid('delete'.$document->getSlug(), $request->get('_token'))){
                // Suppression de l'objet Document
                $this->entityManagerInterface->remove($document);
                // Suppression du fichier de l'objet Document dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$document->getDocumentFile());
                // Enregistrement en base de données
                $this->entityManagerInterface->flush($document);
                // Message flash et redirection
                $this->addFlash("success","Le document a été supprimé avec succès !");
                return $this->redirectToRoute('app_admin_document_index');
            }
        }
        // Message flash et redirection
        $this->addFlash("warning", "Impossible de supprimer le document car ce dernier est associé à un ou plusieurs services ou articles");
        return $this->redirectToRoute('app_admin_document_index');
    }
}
