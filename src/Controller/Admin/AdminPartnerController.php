<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Services\UploadFile;
use App\Form\SearchPartnerType;
use App\Services\SearchPartner;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPartnerController extends AbstractController
{
    private $entityManagerInterface;
    private $sluggerInterface;
    private $uploadFile;

    public function __construct(EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface, UploadFile $uploadFile)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->sluggerInterface = $sluggerInterface;
        $this->uploadFile = $uploadFile;
    }
    
    #[Route('/admin/partner/new', name:'app_admin_partner_new')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de création d'un objet Partner
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        // Création d'une nouvelle instance de la classe Partner
        $partner = new partner;
        // Construction du formulaire de création d'un objet partner
        $form = $this->createForm(PartnerType::class, $partner);
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel fichier image de couverture de l'objet Partner envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Si un fichier image a bien été transmis
            if($coverImage) {
                // Upload du nouveau fichier d'image de couverture de l'objet Partner
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet partner
                $partner->setCoverImage($newCoverImage);
            }
            // Mise à jour de la propriété slug de l'objet partner
            $partner->setSlug(strtolower($this->sluggerInterface->slug($partner->getName())));
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->persist($partner);
            $this->entityManagerInterface->flush();
            // Message flash et redirection
            $this->addFlash("success", "Le partenaire a été créé avec succès !");
            return $this->redirectToRoute('app_admin_partner_index');
        }
        $formView = $form->createView();
        return $this->render('admin/partner/new.html.twig', [
            'formView' => $formView
        ]);
    }
    
    #[Route('/admin/partner/index', name: 'app_admin_partner_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Partner
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param PartnerRepository $partnerRepository
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    public function index(Request $request, PartnerRepository $partnerRepository, PaginatorInterface $paginatorInterface): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Partner
        $searchPartner = new SearchPartner;
        // Création du formulaire de recherche d'objets Partner
        $form = $this->createForm(SearchPartnerType::class, $searchPartner);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Partner sélectionnés à l'aide des propriétés de l'objet SearchPartner
        $partnersData = $partnerRepository->findWithSearchPartner($searchPartner);
        // Pagination des objets Partner
        $partners = $paginatorInterface->paginate(
            // Objets Partner récupérés
            $partnersData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets partner à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners,
            'partnersData' => $partnersData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/partner/{slug}/edit', name:'app_admin_partner_edit')]
    /**
     * Contrôle l'affichage et le traitement du formulaire de modification d'un objet Partner
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param PartnerRepository $partnerRepository
     * @return Response
     */
    public function edit($slug, Request $request, PartnerRepository $partnerRepository): Response
    {
        // Récupération de l'objet Partner à modifier
        $partner = $partnerRepository->findOneBy([
            'slug' => $slug
        ]);
        // Vérification de l'existence de l'objet Partner à modifier
        if(!$partner) {
            throw $this->createNotFoundException("Le partenaire demandé n'existe pas !");
        }
        // Construction du formulaire de modification de l'objet Partner
        $form = $this->createForm(PartnerType::class, $partner);
        // Récupération de l'ancien fichier d'image de couverture de l'objet Partner
        $oldCoverImage = $partner->getCoverImage();
        // Analyse de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération d'un éventuel nouveau fichier d'image de couverture de l'objet Partner envoyé par le formulaire
            $coverImage = $form->get('coverImage')->getData();
            // Vérification de l'existence d'un nouveau fichier d'image de couverture de l'objet Partner transmis par le formulaire
            if($coverImage) {
                // Si un ancien fichier d'image de couverture l'objet Partner existe
                if($oldCoverImage) {
                    // Suppression de l'ancien fichier d'image de couverture l'objet Partner dans le répertoire cible
                    unlink($this->getParameter('uploads_directory').'/'.$oldCoverImage);
                }
                // Upload du nouveau fichier d'image de couverture de l'objet Partner
                $newCoverImage = $this->uploadFile->upload($coverImage);
                // Mise à jour de la propriété coverImage de l'objet Partner
                $partner->setCoverImage($newCoverImage);
            } else {
                // Si aucun nouveau fichier d'image de couverture de l'objet Partner n'a été transmis, on conserve l'ancien
                $partner->setCoverImage($oldCoverImage);
            }
            // Affectation du slug à l'objet partner
            $partner->setSlug(strtolower($this->sluggerInterface->slug($partner->getName())));
            // Affectation de la date de mise à jour à l'objet Partner
            $partner->setUpdatedAt(new \Datetime);
            // Sauvegarde et envoi en base de données
            $this->entityManagerInterface->flush($partner);
            // Message flash et redirection
            $this->addFlash("success", "Le partenaire a été modifié avec succès !");
            return $this->redirectToRoute('app_admin_partner_index');
        }
        $formView = $form->createView();
        return $this->render('admin/partner/edit.html.twig', [
            'formView' => $formView,
            'partner' => $partner
        ]);
    }

    #[Route('/admin/partner/{slug}/detail', name:'app_admin_partner_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Partner
     * @IsGranted("ROLE_ADMIN")
     *
     * @param [type] $slug
     * @param Request $request
     * @param PartnerRepository $partnerRepository
     * @return Response
     */
    public function detail($slug, PartnerRepository $partnerRepository): Response
    {
        // Récupération de l'objet Partner à afficher
        $partner = $partnerRepository->findOneBy([
            'slug' => $slug
        ]);
        return $this->render('/admin/partner/detail.html.twig', [
            'partner' => $partner
        ]);
    }

    #[Route('/admin/partner/{slug}/delete', name:'app_admin_partner_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Partner
     *
     * @param [type] $slug
     * @param PartnerRepository $partnerRepository
     * @return Response
     */
    public function delete($slug, Request $request, partnerRepository $partnerRepository): Response
    {
        // Récupération de l'objet Partner à supprimer
        $partner = $partnerRepository->findOneBy([
            'slug' =>$slug
        ]);
        // Récupération du fichier d'image de couverture de l'objet Partner à supprimer
        $coverImage = $partner->getCoverImage();
        // Vérification de l'existence de l'objet Partner à supprimer
        if(!$partner){
            throw $this->createNotFoundException("Le partenaire demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Partner
        if($this->isCsrfTokenValid('delete'.$partner->getSlug(), $request->get('_token'))){
            // Suppression de l'objet Partner
            $this->entityManagerInterface->remove($partner);
            // Si l'objet Partner à supprimer possède un fichier d'image de couverture
            if($coverImage) {
                // Suppression du fichier d'image de couverture de l'objet Partner dans le répertoire cible
                unlink($this->getParameter('uploads_directory').'/'.$coverImage);
            }
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($partner);
            // Message flash et redirection
            $this->addFlash("success","Le partenaire a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_partner_index');
        }
    }
}

