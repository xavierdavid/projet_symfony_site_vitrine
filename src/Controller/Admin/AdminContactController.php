<?php

namespace App\Controller\Admin;

use Knp\Component\Pager\Paginator;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminContactController extends AbstractController
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface) {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    #[Route('/admin/contact/index', name: 'app_admin_contact_index')]
    /**
     * Contrôle l'affichage de la page d'index des objets Contact
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param PaginatorInterface $paginatorInterface
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, ContactRepository $contactRepository): Response
    {
        // Récupération des objets Contact en base de données
        $contactsData = $contactRepository->findBy([], [
            'createdAt' => 'DESC'
        ]);
        // Pagination des objets Contact
        $contacts = $paginatorInterface->paginate(
            // Objets Contact récupérés
            $contactsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Contact à afficher par page
            10
        );

        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,
            'contactsData' => $contactsData
        ]);
    }

    #[Route('/admin/contact/{id}/delete', name:'app_admin_contact_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Contact
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param [type] $id
     * @param contactRepository $contactRepository
     * @return Void
     */
    public function delete($id, Request $request, ContactRepository $contactRepository)
    {
        // Récupération de l'objet Contact à supprimer
        $contact = $contactRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Contact à supprimer
        if(!$contact){
            throw $this->createNotFoundException("Le message demandé n'existe pas !");
        }
        // Test du token autorisant la suppression de l'objet Contact
        if($this->isCsrfTokenValid('delete'.$contact->getId(), $request->get('_token'))){
            // Suppression de l'objet Contact
            $this->entityManagerInterface->remove($contact);
            // Enregistrement en base de données
            $this->entityManagerInterface->flush($contact);
            // Message flash et redirection
            $this->addFlash("success","Le message a été supprimé avec succès !");
            return $this->redirectToRoute('app_admin_contact_index');
        }
    }
}
