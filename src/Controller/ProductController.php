<?php

namespace App\Controller;

use App\Form\SearchProductType;
use App\Repository\MetatagRepository;
use App\Services\SearchProduct;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name:'app_products')]
    /**
     * Contrôle l'affichage de la page 'Services' du site
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param MetatagRepository $metatagRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository,MetatagRepository $metatagRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Récupération de l'objet Metatag de la page 'Services'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Services'
        ]);
        // Récupération des objets Product triés par ordre priorité
        $productsData = $productRepository->findBy([],['priorityOrder' => 'ASC']);
        // Pagination des objets Product
        $products = $paginatorInterface->paginate(
            // Objets Product récupérés
            $productsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Product à afficher par page
            3
        );
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'productsData' => $productsData,
            'metatag' => $metatag
        ]);
    }

    #[Route('/product/{slug}/detail', name:'app_product_detail')]
    /**
     * Contrôle l'affichage d'un objet Product à partir de la page 'Services'
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
        // Vérification de l'existence de l'objet Product à afficher
        if(!$product) {
            throw $this->createNotFoundException("Le service demandé n'existe pas !");
        }
        // Récupération des objets Image associés à l'objet Product
        $images = $product->getImages();
        // Récupération des objets Document associés à l'objet Product
        $documents = $product->getDocuments();
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'images' => $images,
            'documents' => $documents,
        ]);
    }
}
