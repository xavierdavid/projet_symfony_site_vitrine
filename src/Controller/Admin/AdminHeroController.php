<?php

namespace App\Controller\Admin;

use App\Entity\Hero;
use App\Form\HeroType;
use App\Services\UploadFile;
use App\Repository\HeroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHeroController extends AbstractController
{
    private $entityManagerInterface;
    private $uploadFile;

    public function __construct(EntityManagerInterface $entityManagerInterface, UploadFile $uploadFile)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->uploadFile = $uploadFile;
    }

    #[Route('/admin/hero/index', name: 'app_admin_hero_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Hero
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param HeroRepository $herorRepository
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
        return $this->render('admin/hero/index.html.twig', [
            'hero' => $hero
        ]);
    }

    #[Route('/admin/hero/{id}/edit', name:'app_admin_hero_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Hero
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $id
     * @param Request $request
     * @param HeroRepository $heroRepository
     * @return Response
     */
    public function edit($id, Request $request, HeroRepository $heroRepository): Response
    {
        // Récupération de l'objet Hero à modifier
        $hero = $heroRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Hero à modifier
        if(!$hero) {
            throw $this->createNotFoundException("La section Hero demandée n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Hero
        $form = $this->createForm(HeroType::class, $hero);
        // Récupération de l'ancien fichier d'image principale de l'objet Hero
        $oldMasterImage = $hero->getMasterImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image principale de l'objet Hero envoyé par le formulaire
            $masterImage = $form->get('masterImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image principale de l'objet Hero transmis par le formulaire
            if($masterImage) {
                // Si un ancien fichier d'image principale de l'objet Hero existe
                if($oldMasterImage) {
                    // Suppression de l'ancien fichier d'image princpale de l'objet Hero dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldMasterImage);
                }
                // Upload du nouveau fichier d'image principale de l'objet Hero
                $newMasterImage = $this->uploadFile->upload($masterImage);
                // Mise à jour de la propriété masterImage de l'objet Hero
                $hero->setMasterImage($newMasterImage);
            } else {
                // Si aucun nouveau fichier d'image principale de l'objet Hero n'a été transmis, on conserve l'ancien
                $hero->setMasterImage($oldMasterImage);
            }
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($hero);
            // Message flash et redirection
            $this->addFlash("success", "La section Hero a été modifiée avec succès !");
            return $this->redirectToRoute('app_admin_hero_index');
        }
        $formView = $form->createView();
        return $this->render('admin/hero/edit.html.twig', [
            'formView' => $formView,
            'hero' => $hero
        ]);
    }
}
