<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Metatag;
use App\Entity\Organization;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }
    
    public function load(ObjectManager $manager): void
    {
        // Création d'un objet Utilisateur avec un rôle Admin 
        $admin = new User;
        // Hashage du mot de passe
        $hashPassword = $this->userPasswordHasherInterface->hashPassword($admin, 'password');
        $admin  ->setEmail("xav.david28@gmail.com")
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hashPassword)
                ->setFirstname("Xavier")
                ->setLastname("DAVID");
        $manager->persist($admin);
        // Création d'un objet Organization
        $organization = new Organization; 
        $organization 
            ->setOrganizationName('organisation test')
            ->setSiteTitle('site test')
            ->setAddress('3 rue des tilleuls')
            ->setPostal('75000')
            ->setCity('Paris')
            ->setCountry(('France'))
            ->setPhone('0237356600')
            ->setShortDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.')
            ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
            ->setAdministratorFirstname('Xavier')
            ->setAdministratorLastname('DAVID'); 
        $manager->persist($organization);
        // Création d'un objet Metatag pour la page 'Accueil' du site
        $homeMetatag = new Metatag;
        $homeMetatag
            ->setPageName('Accueil')
            ->setTitle("Titre de la page 'Accueil'")
            ->setMetaDescription("Description de la page d'accueil");
        $manager->persist($homeMetatag);
        // Création d'un objet Metatag pour la page 'A propos' du site
        $aProposMetatag = new Metatag;
        $aProposMetatag
            ->setPageName('A propos')
            ->setTitle("Titre de la page 'A propos'")
            ->setMetaDescription("Description de la page 'A propos'");
        $manager->persist($aProposMetatag);
        // Création d'un objet Metatag pour la page 'Services' du site
        $servicesMetatag = new Metatag;
        $servicesMetatag
            ->setPageName('Services')
            ->setTitle("Titre de la page 'Services'")
            ->setMetaDescription("Description de la page 'Services'");
        $manager->persist($servicesMetatag);
        // Création d'un objet Metatag pour la page 'Articles' du site
        $articlesMetatag = new Metatag;
        $articlesMetatag
            ->setPageName('Articles')
            ->setTitle("Titre de la page 'Articles'")
            ->setMetaDescription("Description de la page 'Articles'");
        $manager->persist($articlesMetatag);
        // Création d'un objet Metatag pour la page 'Contact' du site
        $contactMetatag = new Metatag;
        $contactMetatag
            ->setPageName('Contact')
            ->setTitle("Titre de la page 'Contact'")
            ->setMetaDescription("Description de la page 'Contact'");
        $manager->persist($contactMetatag);
        // Envoi en base de données
        $manager->flush();
    }
}
