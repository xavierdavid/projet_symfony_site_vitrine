<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Form\ImageType;
use App\Services\UploadFile;
use App\Form\SearchImageType;
use App\Services\SearchImage;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminImageController extends AbstractController
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
    
    #[Route('/admin/image/new', name:'app_admin_image_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Image
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        // Création d'une nouvelle instance de la classe Image
        $image = new Image;
        // Construction du formulaire de création d'un objet Image
        $form = $this->createForm(ImageType::class, $image);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier de l'objet Image envoyé par le formulaire
            $imageFile = $form->get('imageFile')->getData();
            // Si aucun fichier n'a été envoyé
            if(!$imageFile) {
                // Message flash et redirection
                $this->addFlash("warning", "Vous devez joindre un fichier image !");
                return $this->redirectToRoute('app_admin_image_new');
            }
            // Upload du fichier de l'objet Image
            $newImageFile = $this->uploadFile->upload($imageFile);
            // Affectation des valeurs aux propriétés de l'objet Image
            $image->setSlug(strtolower($this->sluggerInterface->slug($image->getMediaTitle())));
            $image->setImageFile($newImageFile);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($image);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le média a été créé avec succès !");
            return $this->redirectToRoute('app_admin_image_index');
        }
        $formView = $form->createView();
        return $this->render('admin/image/new.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/image/index', name:'app_admin_image_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Image
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @param ImageRepository $ImageRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, ImageRepository $imageRepository): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Image
        $searchImage = new SearchImage;
        // Création du formulaire de recherche d'objets Image
        $form = $this->createForm(SearchImageType::class, $searchImage);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Image sélectionnés à l'aide des propriétés de l'objet SearchImage
        $imagesData = $imageRepository->findWithSearchImage($searchImage);
        // Pagination des objets Image
        $images = $paginatorInterface->paginate(
            // Objets Image récupérés
            $imagesData, 
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page',1),
            // Nombre d'objets Image à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('/admin/image/index.html.twig', [
            'images' => $images,
            'imagesData' => $imagesData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/image/{slug}/edit', name:'app_admin_image_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Image
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param [type] $slug
     * @param ImageRepository $ImageRepository
     * @return Response
     */
    public function edit($slug, Request $request, ImageRepository $ImageRepository): Response
    {
        // Récupération de l'objet Image à modifier
        $image = $ImageRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Image à modifier
        if(!$image){
            throw $this->createNotFoundException("Le média demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Image
        $form = $this->createForm(ImageType::class, $image);
        // Récupération de l'ancien fichier de l'objet Image
        $oldImageFile = $image->getImageFile();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier de l'objet Image envoyé par le formulaire
            $imageFile = $form->get('imageFile')->getData();
            // Vérification de l'existence d'un nouveau fichier de l'objet Image transmis par le formulaire
            if($imageFile) {
                // Si un ancien fichier de l'objet Image existe
                if($oldImageFile) {
                    // Suppression de l'ancien fichier de l'objet Image dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldImageFile);
                }
                // Upload du nouveau fichier de l'objet Image
                $newImageFile = $this->uploadFile->upload($imageFile);
                // Mise à jour de la propriété ImageFile de l'objet Image
                $image->setImageFile($newImageFile);
            } else {
                // Si aucun nouveau fichier de l'objet Image n'a été transmis, on conserve l'ancien
                $image->setImageFile($oldImageFile);
            }
            // Affectation du slug à l'objet Image
            $image->setSlug(strtolower($this->sluggerInterface->slug($image->getMediaTitle())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($image);
            // Message flash et redirection
            $this->addFlash("success", "Le média a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_image_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/image/edit.html.twig', [
            'formView' => $formView,
            'image' => $image
        ]);  
    }

    #[Route('/admin/image/{slug}/detail', name:'app_admin_image_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Image
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $slug
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function detail($slug, ImageRepository $imageRepository): Response
    {
        // Récupération de l'objet Image à afficher
        $image = $imageRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Image à afficher
        if(!$image){
            throw $this->createNotFoundException("Le média demandé n'existe pas !");
        }
        // Récupération des objets Article associés à l'objet Image
        $articles = $image->getArticles();
        // Récupération des objets Product associés à l'objet Image
        $products = $image->getProducts();
        return $this->render('/admin/image/detail.html.twig', [
            'image' => $image,
            'articles' => $articles,
            'products' => $products
        ]);
    }

    #[Route('/admin/image/{slug}/delete', name:'app_admin_image_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Image
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param [type] $slug
     * @param ImageRepository $ImageRepository
     * @return Void
     */
    public function delete($slug, Request $request, ImageRepository $ImageRepository)
    {
        // Récupération de l'objet Image à supprimer
        $image = $ImageRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Image à supprimer
        if(!$image){
            throw $this->createNotFoundException("le média demandé n'existe pas !");
        }
        // Récupération des objets Article associés à l'objet Image
        $articles = $image->getArticles();
        // Récupération des objets Product associés à l'objet Image
        $products = $image->getProducts();
        // Vérification de l'existence d'objets Article ou Product associés à l'objet Image
        if($articles->isEmpty() && $products->isEmpty()) {
            // Test du token autorisant la suppression de l'objet Image
            if($this->isCsrfTokenValid('delete'.$image->getSlug(), $request->get('_token'))){
                // Suppression de l'objet Image
                $this->entityManagerInterface->remove($image);
                // Suppression du fichier de l'objet Image dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$image->getImageFile());
                // Enregistrement en base de données
                $this->entityManagerInterface->flush($image);
                // Message flash et redirection
                $this->addFlash("success","Le média a été supprimé avec succès !");
                return $this->redirectToRoute('app_admin_image_index');
            }
        }
        // Message flash et redirection
        $this->addFlash("warning", "Impossible de supprimer le média car ce dernier est associé à un ou plusieurs services ou articles");
        return $this->redirectToRoute('app_admin_image_index');
    }
}
