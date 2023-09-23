<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\MetatagRepository;
use App\Repository\PartnerRepository;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(MetatagRepository $metatagRepository, ArticleRepository $articleRepository,PartnerRepository $partnerRepository, Request $request): Response
    {
        // Récupération de l'objet Metatag de la page 'Accueil'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Accueil'
        ]);
        // Récupération des objets Article à la une triés par ordre priorité et limité à 3 articles
        $articles = $articleRepository->findBy(['isFrontPage' => true,],['priorityOrder' => 'ASC'],3);

        // Récupération des objets Partner triés par ordre de priorité
        $partners = $partnerRepository->findAll(['priorityOrder' => 'ASC'], ['updatedAt' => 'ASC']);
        
        return $this->render('home/index.html.twig', [
            'metatag' => $metatag,
            'articles' => $articles,
            'partners' => $partners
        ]);
    }
}
