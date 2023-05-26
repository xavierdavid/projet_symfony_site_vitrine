<?php

namespace App\Controller;

use App\Repository\MetatagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    /**
     * Contrôle l'affichage de la page 'A propos' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository): Response
    {
        // Récupération de l'objet Metatag de la page 'A propos'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'A propos'
        ]);
        return $this->render('about/index.html.twig', [
            'metatag' => $metatag
        ]);
    }
}
