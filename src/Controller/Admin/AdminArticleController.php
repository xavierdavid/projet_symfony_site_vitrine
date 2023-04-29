<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Services\UploadFile;
use App\Form\SearchArticleType;
use App\Services\SearchArticle;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminArticleController extends AbstractController
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
    
    #[Route('/admin/article/new', name:'app_admin_article_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Article
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        // Création d'une nouvelle instance de la classe Article
        $article = new Article;
        // Construction du formulaire de création d'un objet Article
        $form = $this->createForm(ArticleType::class, $article);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel fichier image de couverture de l'objet Article envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Si un fichier image a bien été transmis
            if($coverImage) {
                // Upload du nouveau fichier d'image de couverture de l'objet Article
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Article
                $article->setCoverImage($newCoverImage);
            }
            // Mise à jour de la propriété slug de l'objet Article
            $article->setSlug(strtolower($this->sluggerInterface->slug($article->getTitle())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($article);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "L'article a été créé avec succès !");
            return $this->redirectToRoute('app_admin_article_index');
        }
        $formView = $form->createView();
        return $this->render('admin/article/new.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/article/index', name: 'app_admin_article_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Article
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginatorInterface): Response
    {
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
            10
        );
        $formView = $form->createView();
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
            'articlesData' => $articlesData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/article/{slug}/edit', name:'app_admin_article_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Article
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function edit($slug, Request $request, ArticleRepository $articleRepository): Response
    {
        // Récupération de l'objet Article à modifier
        $article = $articleRepository->findOneBy([
            'slug' => $slug
        ]);
        // Récupération des objets Image associés à l'objet Article
        $images = $article->getImages();
        // Vérification de l'existence de l'objet Article à modifier
        if(!$article) {
            throw $this->createNotFoundException("L'article demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Article
        $form = $this->createForm(ArticleType::class, $article);
        // Récupération de l'ancien fichier d'image de couverture de l'objet Article
        $oldCoverImage = $article->getCoverImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image de couverture de l'objet Article envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image de couverture de l'objet Article transmis par le formulaire
            if($coverImage) {
                // Si un ancien fichier d'image de couverture l'objet Article existe
                if($oldCoverImage) {
                    // Suppression de l'ancien fichier d'image de couverture l'objet Article dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldCoverImage);
                }
                // Upload du nouveau fichier d'image de couverture de l'objet Article
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Article
                $article->setCoverImage($newCoverImage);
            } else {
                // Si aucun nouveau fichier d'image de couverture de l'objet Article n'a été transmis, on conserve l'ancien
                $article->setCoverImage($oldCoverImage);
            }
            // Affectation du slug à l'objet Article
            $article->setSlug(strtolower($this->sluggerInterface->slug($article->getTitle())));
            // Affectation de la date de mise à jour de l'objet Article
            $article->setUpdatedAt(new \Datetime);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($article);
            // Message flash et redirection
            $this->addFlash("success", "L'article a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_article_index');
        }
        $formView = $form->createView();
        return $this->render('admin/article/edit.html.twig', [
            'formView' => $formView,
            'article' => $article,
            'images' => $images
        ]);
    }

    #[Route('/admin/article/{slug}/detail', name:'app_admin_article_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Article
     * @IsGranted("ROLE_ADMIN")
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
        // Récupération des objets Image associés à l'objet Article
        $images = $article->getImages();
        // Récupération des objets Document associés à l'objet Article
        $documents = $article->getDocuments();
        // Récupération des objets Category associés à l'objet Article
        $categories = $article->getCategories();
        return $this->render('/admin/article/detail.html.twig', [
            'article' => $article,
            'images' => $images,
            'documents' => $documents,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/article/{slug}/delete', name:'app_admin_article_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Article
     *
     * @param [type] $slug
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function delete($slug, Request $request, ArticleRepository $articleRepository): Response
    {
        // Récupération de l'objet Article à supprimer
        $article = $articleRepository->findOneBy([
            'slug' =>$slug
        ]);
        // Récupération du fichier d'image de couverture de l'objet Article à supprimer
        $coverImage = $article->getCoverImage();
        // Vérification de l'existence de l'objet Article à supprimer
        if(!$article){
            throw $this->createNotFoundException("L'article demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Article
        if($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->get('_token'))){
            // Suppression de l'objet Article
            $this->entityManagerInterface->remove($article);
            // Si l'objet Article à supprimer possède un fichier d'image de couverture
            if($coverImage) {
                // Suppression du fichier d'image de couverture de l'objet Article dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$coverImage);
            }
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($article);
            // Message flash et redirection
            $this->addFlash("success","L'article a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_article_index');
        }
    }
}
