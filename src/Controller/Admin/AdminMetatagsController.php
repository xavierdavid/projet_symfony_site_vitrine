<?php

namespace App\Controller\Admin;

use App\Form\MetatagsType;
use App\Repository\MetatagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMetatagsController extends AbstractController
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface) {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/admin/metatags/index', name:'app_admin_metatags_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Metatags
     * @IsGranted("ROLE_ADMIN")
     *
     * @param MetatagsRepository $metatagsRepository
     * @return Response
     */
    public function index(MetatagsRepository $metatagsRepository): Response
    {
        // Récupération des objets Metatags en base de données
        $metatags = $metatagsRepository->findAll();

        return $this->render('admin/metatags/index.html.twig', [
            'metatags' => $metatags
        ]);
    }

    #[Route('/admin/metatags/{id}/edit', name:'app_admin_metatags_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Metatags
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $id
     * @param metatagsRepository $metatagsRepository
     * @return Response
     */
    public function edit($id, Request $request, MetatagsRepository $metatagsRepository): Response
    {
        // Récupération de l'objet Metatags à modifier
        $metatags = $metatagsRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Metatags à modifier
        if(!$metatags){
            throw $this->createNotFoundException("Le metatags demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Metatags
        $form = $this->createForm(MetatagsType::class, $metatags);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($metatags);
             // Message flash et redirection
             $this->addFlash("success", "Les informations de référencement SEO du site ont été modifiées avec succès");
             return $this->redirectToRoute('app_admin_metatags_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/metatags/edit.html.twig', [
            'formView' => $formView,
            'metatags' => $metatags
        ]);
    }
}
