<?php

namespace App\Controller\Admin;

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
    public function index(): Response
    {
        return $this->render('admin/home/index.html.twig', [
            
        ]);
    }
}
