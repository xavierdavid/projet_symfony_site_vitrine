<?php

namespace App\Controller;

use App\Repository\MetatagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalNoticeController extends AbstractController
{
    #[Route('/legal/notice', name: 'app_legal_notice')]
    /**
     * Contrôle l'affichage de la page 'Mentions légales' du site
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository): Response
    {
        // Récupération de l'objet Metatag de la page 'Mentions légales'
        $metatag = $metatagRepository->findOneBy([
            'pageName' => 'Mentions légales'
        ]);
        return $this->render('legal_notice/index.html.twig', [
            'metatag' => $metatag
        ]);
    }
}
