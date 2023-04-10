<?php

namespace App\Controller\Admin;

use App\Form\MetatagType;
use App\Repository\MetatagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMetatagController extends AbstractController
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface) {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/admin/metatag/index', name:'app_admin_metatag_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Metatag
     * @IsGranted("ROLE_ADMIN")
     *
     * @param MetatagRepository $metatagRepository
     * @return Response
     */
    public function index(MetatagRepository $metatagRepository): Response
    {
        // Récupération des objets Metatag en base de données
        $metatags = $metatagRepository->findAll();

        return $this->render('admin/metatag/index.html.twig', [
            'metatags' => $metatags
        ]);
    }

    #[Route('/admin/metatag/{id}/edit', name:'app_admin_metatag_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Metatag
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $id
     * @param metatagRepository $metatagRepository
     * @return Response
     */
    public function edit($id, Request $request, MetatagRepository $metatagRepository): Response
    {
        // Récupération de l'objet Metatag à modifier
        $metatag = $metatagRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Metatag à modifier
        if(!$metatag){
            throw $this->createNotFoundException("Le metatag demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Metatag
        $form = $this->createForm(MetatagType::class, $metatag);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($metatag);
             // Message flash et redirection
             $this->addFlash("success", "Les informations de référencement SEO de la page '".$metatag->getPageName()."' ont été modifiées avec succès !");
             return $this->redirectToRoute('app_admin_metatag_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/metatag/edit.html.twig', [
            'formView' => $formView,
            'metatag' => $metatag
        ]);
    }
}
