<?php

namespace App\Controller;

use App\Form\SearchArticleType;
use App\Services\SearchArticle;
use App\Repository\ArticleRepository;
use App\Repository\MetatagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/articles', name:'app_articles')]
    /**
     * Contrôle l'affichage de la page 'Actualités' du site
     *
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param MetatagRepository $metatagRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, ArticleRepository $articleRepository, MetatagRepository $metatagRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Récupération de l'objet Metatag de la page 'Actualités'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Articles'
        ]);
        // Instanciation d'un nouvel objet de recherche d'objets Article
        $searchArticle = new SearchArticle;
        // Création du formulaire de recherche d'objets Article
        $form = $this->createForm(SearchArticleType::class, $searchArticle);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Article sélectionnés à l'aide des propriétés de l'objet SearchArticle
        $articlesData = $articleRepository->findWithSearchArticle($searchArticle);
        // Pagination des objets Article
        $articles = $paginatorInterface->paginate(
            // Objets Article récupérés
            $articlesData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Article à afficher par page
            3
        );
        $formView = $form->createView();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'articlesData' => $articlesData,
            'formView' => $formView,
            'metatag' => $metatag
        ]);
    }

    #[Route('/article/{slug}/detail', name:'app_article_detail')]
    /**
     * Contrôle l'affichage d'un objet Article à partir de la page 'Actualités'
     *
     * @param [type] $slug
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function detail($slug, ArticleRepository $articleRepository): Response
    {
        // Récupération de l'objet Article à afficher
        $article = $articleRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Article à afficher
        if(!$article) {
            throw $this->createNotFoundException("L'article demandé n'existe pas !");
        }
        // Récupération des objets Image associés à l'objet Article
        $images = $article->getImages();
        // Récupération des objets Document associés à l'objet Article
        $documents = $article->getDocuments();
        // Récupération des objets Category associés à l'objet Article
        $categories = $article->getCategories();
        return $this->render('article/detail.html.twig', [
            'article' => $article,
            'images' => $images,
            'documents' => $documents,
            'categories' => $categories
        ]);
    }
}
