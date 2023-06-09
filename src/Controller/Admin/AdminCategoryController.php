<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\SearchCategoryType;
use App\Services\SearchCategory;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    private $sluggerInterface;
    private $entityManagerInterface;

    public function __construct(SluggerInterface $sluggerInterface, EntityManagerInterface $entityManagerInterface) {
        $this->sluggerInterface = $sluggerInterface;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/admin/category/new', name:'app_admin_category_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet category
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        // Création d'une nouvelle instance de la classe Category
        $category = new Category;
        // Construction du formulaire de création d'un objet Category
        $form = $this->createForm(CategoryType::class, $category);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Affectation du slug à l'objet Category
            $category->setSlug(strtolower($this->sluggerInterface->slug($category->getName())));
            // Affectation du nom à l'objet Category avec la première lettre en majuscule
            $category->setName(ucfirst($category->getName()));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($category);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "La catégorie d'article a été créée avec succès !");
            return $this->redirectToRoute('app_admin_category_index');
        }
        $formView = $form->createView();
        return $this->render('admin/category/new.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/category/index', name:'app_admin_category_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Category
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @param PaginatorInterface $paginatorInterface
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, CategoryRepository $categoryRepository): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Category
        $searchCategory = new SearchCategory;
        // Création du formulaire de recherche d'objets Category
        $form = $this->createForm(SearchCategoryType::class, $searchCategory);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Category sélectionnés à l'aide des propriétés de l'objet SearchCategory
        $categoriesData = $categoryRepository->findWithSearchCategory($searchCategory);
       
        // Pagination des objets Category
        $categories = $paginatorInterface->paginate(
            // Objets Category récupérés
            $categoriesData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Category à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
            'categoriesData' => $categoriesData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/category/{slug}/edit', name:'app_admin_category_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Category
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param [type] $slug
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function edit($slug, Request $request, CategoryRepository $categoryRepository): Response
    {
        // Récupération de l'objet Category à modifier
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Category à modifier
        if(!$category){
            throw $this->createNotFoundException("La categorie demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Category
        $form = $this->createForm(CategoryType::class, $category);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Affectation du slug à l'objet Category
            $category->setSlug(strtolower($this->sluggerInterface->slug($category->getName())));
            // Affectation du nom à l'objet Category avec la première lettre en majuscule
            $category->setName(ucfirst($category->getName()));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($category);
            // Message flash et redirection
            $this->addFlash("success", "La categorie a été modifiée avec succès !");
            return $this->redirectToRoute('app_admin_category_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/category/edit.html.twig', [
            'formView' => $formView,
            'category' => $category
        ]);  
    }

    #[Route('/admin/category/{slug}/delete', name:'app_admin_category_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Category
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @param [type] $slug
     * @param CategoryRepository $categoryRepository
     * @return Void
     */
    public function delete($slug, Request $request, CategoryRepository $categoryRepository)
    {
        // Récupération de l'objet Category à supprimer
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Category à supprimer
        if(!$category){
            throw $this->createNotFoundException("Le categorie demandé n'existe pas !");
        }
        // Récupération des objets Article associés à l'objet Category
        $articles = $category->getArticles();
        // Vérification de l'existence d'objets Article associés à l'objet Category
        if($articles->isEmpty()) {
            // Test du token autorisant la suppression de l'objet Category
            if($this->isCsrfTokenValid('delete'.$category->getSlug(), $request->get('_token'))){
                // Suppression de l'objet Category
                $this->entityManagerInterface->remove($category);
                // Enregistrement en base de données
                $this->entityManagerInterface->flush($category);
                // Message flash et redirection
                $this->addFlash("success","La categorie a été supprimée avec succès !");
                return $this->redirectToRoute('app_admin_category_index');
            }
        }
        // Message flash et redirection
        $this->addFlash("warning", "Impossible de supprimer la catégorie car cette dernière est associée à un ou plusieurs articles");
        return $this->redirectToRoute('app_admin_category_index');    
    }
}
