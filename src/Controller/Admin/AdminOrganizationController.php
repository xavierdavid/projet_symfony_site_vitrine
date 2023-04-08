<?php

namespace App\Controller\Admin;

use App\Services\UploadFile;
use App\Form\OrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrganizationController extends AbstractController
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface, UploadFile $uploadFile)
    {
        $this->entityManagerInterface=$entityManagerInterface;
        $this->uploadFile = $uploadFile;
    }
    
    #[Route('/admin/organization/index', name:'app_admin_organization_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Organization
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param OrganizationRepository $organizationRepository
     * @return Response
     */
    public function index(OrganizationRepository $organizationRepository): Response
    {
        // Récupération des objets Organization en base de données
        $organizations = $organizationRepository->findAll();

        return $this->render('admin/organization/index.html.twig', [
            'organizations' => $organizations
        ]);
    }

    #[Route('/admin/organization/{id}/edit', name:'app_admin_organization_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Organization
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $id
     * @param OrganizationRepository $organizationRepository
     * @return Response
     */
    public function edit($id, Request $request, OrganizationRepository $organizationRepository): Response
    {
        // Récupération de l'objet Organization à modifier
        $organization = $organizationRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Organization à modifier
        if(!$organization){
            throw $this->createNotFoundException("L'organization demandée n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Organization
        $form = $this->createForm(OrganizationType::class, $organization);
        // Récupération de l'ancien fichier image (logo) de l'objet Organization
        $oldLogoFile = $organization->getLogo();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier logo de l'objet Organiztion envoyé par le formulaire
            $logoFile = $form->get('logo')->getData();
            // Vérification de l'existence d'un nouveau fichier lgo de l'objet Organization transmis par le formulaire
            if($logoFile) {
                // Si un ancien fichier de l'objet Organization existe
                if($oldLogoFile) {
                    // Suppression de l'ancien fichier logo de l'objet Organization dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldLogoFile);
                }
                // Upload du nouveau fichier logo de l'objet Organization
                $newLogoFile = $this->uploadFile->upload($logoFile);
                // Mise à jour de la propriété logo de l'objet Organization
                $organization->setLogo($newLogoFile);
            } else {
                // Si aucun nouveau fichier logo de l'objet Organization n'a été transmis, on conserve l'ancien
                $organization->setLogo($oldLogoFile);
            }
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($organization);
             // Message flash et redirection
             $this->addFlash("success", "L'organisation a été modifiée avec succès");
             return $this->redirectToRoute('app_admin_organization_index');
        }
        $formView = $form->createView();
        return $this->render('/admin/organization/edit.html.twig', [
            'formView' => $formView,
            'organization' => $organization
        ]);
    }
}
