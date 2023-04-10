<?php

namespace App\Controller;

use App\Repository\MetatagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    /**
     * Contrôle l'affichage de la page 'Acueil' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository): Response
    {
        // Récupération de l'objet Metatag de la page 'Accueil'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Accueil'
        ]);
        
        return $this->render('home/index.html.twig', [
            'metatag' => $metatag
        ]);
    }
}
