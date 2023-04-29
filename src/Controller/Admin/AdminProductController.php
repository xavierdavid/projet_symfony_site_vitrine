<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Services\UploadFile;
use App\Form\SearchProductType;
use App\Services\SearchProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
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
    
    #[Route('/admin/product/new', name:'app_admin_product_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Product
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        // Création d'une nouvelle instance de la classe Product
        $product = new Product;
        // Construction du formulaire de création d'un objet Product
        $form = $this->createForm(ProductType::class, $product);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel fichier image de couverture de l'objet Product envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Si un fichier image a bien été transmis
            if($coverImage) {
                // Upload du nouveau fichier d'image de couverture de l'objet Product
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Product
                $product->setCoverImage($newCoverImage);
            }
            // Mise à jour de la propriété slug de l'objet Product
            $product->setSlug(strtolower($this->sluggerInterface->slug($product->getName())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($product);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le service a été créé avec succès !");
            return $this->redirectToRoute('app_admin_product_index');
        }
        $formView = $form->createView();
        return $this->render('admin/product/new.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/product/index', name: 'app_admin_product_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Product
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param ProductRepository $ProductRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Product
        $searchProduct = new SearchProduct;
        // Création du formulaire de recherche d'objets Product
        $form = $this->createForm(SearchProductType::class, $searchProduct);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Product sélectionnés à l'aide des propriétés de l'objet SearchProduct
        $productsData = $productRepository->findWithSearchProduct($searchProduct);
        // Pagination des objets Product
        $products = $paginatorInterface->paginate(
            // Objets Product récupérés
            $productsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Product à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/Product/index.html.twig', [
            'products' => $products,
            'productsData' => $productsData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/product/{slug}/edit', name:'app_admin_product_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Product
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param ProductRepository $ProductRepository
     * @return Response
     */
    public function edit($slug, Request $request, ProductRepository $productRepository): Response
    {
        // Récupération de l'objet Product à modifier
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);
        // Récupération des objets Image associés à l'objet Product
        $images = $product->getImages();
        // Vérification de l'existence de l'objet Product à modifier
        if(!$product) {
            throw $this->createNotFoundException("Le service demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Product
        $form = $this->createForm(ProductType::class, $product);
        // Récupération de l'ancien fichier d'image de couverture de l'objet Product
        $oldCoverImage = $product->getCoverImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image de couverture de l'objet Product envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image de couverture de l'objet Product transmis par le formulaire
            if($coverImage) {
                // Si un ancien fichier d'image de couverture l'objet Product existe
                if($oldCoverImage) {
                    // Suppression de l'ancien fichier d'image de couverture l'objet Product dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldCoverImage);
                }
                // Upload du nouveau fichier d'image de couverture de l'objet Product
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Product
                $product->setCoverImage($newCoverImage);
            } else {
                // Si aucun nouveau fichier d'image de couverture de l'objet Product n'a été transmis, on conserve l'ancien
                $product->setCoverImage($oldCoverImage);
            }
            // Affectation du slug à l'objet Product
            $product->setSlug(strtolower($this->sluggerInterface->slug($product->getName())));
            // Affectation de la date de mise à jour de l'objet Product
            $product->setUpdatedAt(new \Datetime);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($product);
            // Message flash et redirection
            $this->addFlash("success", "Le service a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_product_index');
        }
        $formView = $form->createView();
        return $this->render('admin/product/edit.html.twig', [
            'formView' => $formView,
            'product' => $product,
            'images' => $images
        ]);
    }

    #[Route('/admin/product/{slug}/detail', name:'app_admin_product_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Product
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param ProductRepository $ProductRepository
     * @return Response
     */
    public function detail($slug, ProductRepository $productRepository): Response
    {
        // Récupération de l'objet Product à afficher
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);
        // Récupération des objets Image associés à l'objet Product
        $images = $product->getImages();
        // Récupération des objets Document associés à l'objet Product
        $documents = $product->getDocuments();
        return $this->render('/admin/Product/detail.html.twig', [
            'product' => $product,
            'images' => $images,
            'documents' => $documents
        ]);
    }

    #[Route('/admin/product/{slug}/delete', name:'app_admin_product_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Product
     *
     * @param [type] $slug
     * @param ProductRepository $ProductRepository
     * @return Response
     */
    public function delete($slug, Request $request, ProductRepository $productRepository): Response
    {
        // Récupération de l'objet Product à supprimer
        $product = $productRepository->findOneBy([
            'slug' =>$slug
        ]);
        // Récupération du fichier d'image de couverture de l'objet Product à supprimer
        $coverImage = $product->getCoverImage();
        // Vérification de l'existence de l'objet Product à supprimer
        if(!$product){
            throw $this->createNotFoundException("Le service demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Product
        if($this->isCsrfTokenValid('delete'.$product->getSlug(), $request->get('_token'))){
            // Suppression de l'objet Product
            $this->entityManagerInterface->remove($product);
            // Si l'objet Product à supprimer possède un fichier d'image de couverture
            if($coverImage) {
                // Suppression du fichier d'image de couverture de l'objet Product dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$coverImage);
            }
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($product);
            // Message flash et redirection
            $this->addFlash("success","Le service a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_product_index');
        }
    }
}
