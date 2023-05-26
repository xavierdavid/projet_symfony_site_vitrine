<?php

namespace App\Controller\Admin;

use App\Form\SearchContactType;
use App\Services\SearchContact;
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
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param Request $request
     * @param PaginatorInterface $paginatorInterface
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginatorInterface, ContactRepository $contactRepository): Response
    {
        // Instanciation d'un nouvel objet de recherche d'objets Contact
        $searchContact = new SearchContact;
        // Création du formulaire de recherche d'objets Contact
        $form = $this->createForm(SearchContactType::class, $searchContact);
        // Analyse de la requête et traitement du formulaire de recherche
        $form->handleRequest($request);
        // Récupération en base de données des objets Contact sélectionnés à l'aide des propriétés de l'objet SearchContact
        $contactsData = $contactRepository->findWithSearchContact($searchContact);
        // Pagination des objets Contact
        $contacts = $paginatorInterface->paginate(
            // Objets Contact récupérés
            $contactsData,
            // Récupération de la valeur de l'attribut 'page' (page en cours) transmis en 'GET' dans la requête
            $request->query->getInt('page', 1),
            // Nombre d'objets Contact à afficher par page
            10
        );
        $formView = $form->createView();
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,
            'contactsData' => $contactsData,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/contact/{id}/detail', name:'app_admin_contact_detail')]
    /**
     * Contrôle l'affichage de la page d'un objet Contact
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
     *
     * @param [type] $id
     * @param Request $request
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function detail($id, ContactRepository $contactRepository): Response
    {
        // Récupération de l'objet Contact à afficher
        $contact = $contactRepository->findOneBy([
            'id' => $id
        ]);
        // Vérification de l'existence de l'objet Contact à afficher
        if(!$contact){
            throw $this->createNotFoundException("Le message demandé n'existe pas !");
        }
        return $this->render('/admin/contact/detail.html.twig', [
            'contact' => $contact
        ]);
    }

    #[Route('/admin/contact/{id}/delete', name:'app_admin_contact_delete')]
    /**
     * Contrôle le traitement de la suppression d'un objet Contact
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page !")
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
