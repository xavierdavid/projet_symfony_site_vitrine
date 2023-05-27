<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\MetatagRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    /**
     * Contrôle l'affichage de la page 'Acueil' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository, ArticleRepository $articleRepository): Response
    {
        // Récupération de l'objet Metatag de la page 'Accueil'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Accueil'
        ]);
        // Récupération des objets Article à la une triés par ordre priorité
        $articles = $articleRepository->findBy(['isFrontPage' => true,],['priorityOrder' => 'ASC']);
        
        return $this->render('home/index.html.twig', [
            'metatag' => $metatag,
            'articles' => $articles
        ]);
    }
}
