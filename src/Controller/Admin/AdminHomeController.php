<?php

namespace App\Controller\Admin;

use App\Repository\HeroRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHomeController extends AbstractController
{
    #[Route('/admin', name:'app_admin_home')]
    /**
     * Contrôle l'affichage de la page d'accueil de l'interface d'administration
     * @IsGranted("ROLE_AUTHOR", message="Vous n'êtes pas autorisé à accéder à cette page !")
     * 
     * @return Response
     */
    public function index(HeroRepository $heroRepository): Response
    {
        // Récupération du dernier objet Hero inséré en base de données
        $hero = $heroRepository->findBy([], [
            'id' => 'DESC'], // Tri par identifiant et par ordre décroissant
            1, // Limite de 1 enregistrement
            0 // Offset
        );
        return $this->render('admin/home/index.html.twig', [
            'hero' => $hero
        ]);
    }
}
