<?php

namespace App\DataFixtures;

use App\Entity\Hero;
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
            ->setOrganizationName('Space Explorer')
            ->setSiteTitle('Site web de Space Explorer')
            ->setAddress('15 rue de la voie lactée')
            ->setPostal('75000')
            ->setCity('Paris')
            ->setCountry(('France'))
            ->setPhone('0237356600')
            ->setFacebook('https://www.facebook.com')
            ->setInstagram('https://www.instagram.com')
            ->setTwitter('https://www.twitter.com')
            ->setShortDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.')
            ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
            ->setLogo('logo_fusee.png')
            ->setAdministratorFirstname('Xavier')
            ->setAdministratorLastname('DAVID'); 
        $manager->persist($organization);
        // Création d'un objet Hero
        $hero = new Hero;
        $hero
            ->setSlogan('Bienvenue sur le site de Space Explorer !')
            ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.')
            ->setFirstButtonTitle('Bouton 1')
            ->setFirstButtonUrl('#')
            ->setSecondButtonTitle('Bouton 2')
            ->setSecondButtonUrl('#')
            ->setMasterImage('hubble_telescope.jpg')
            ->setMasterImageDescription('Téléscope Hubble dans l\'espace');
        $manager->persist($hero);
        // Création d'un objet Metatag pour la page 'Accueil' du site
        $homeMetatag = new Metatag;
        $homeMetatag
            ->setPageName('Accueil')
            ->setTitle("A la découverte de l'univers !'")
            ->setMetaDescription("Page d'accueil");
        $manager->persist($homeMetatag);
        // Création d'un objet Metatag pour la page 'A propos' du site
        $aProposMetatag = new Metatag;
        $aProposMetatag
            ->setPageName('A propos')
            ->setTitle("Qui sommes-nous ?")
            ->setMetaDescription("Page 'A propos'");
        $manager->persist($aProposMetatag);
        // Création d'un objet Metatag pour la page 'Services' du site
        $servicesMetatag = new Metatag;
        $servicesMetatag
            ->setPageName('Services')
            ->setTitle("Nos services")
            ->setMetaDescription("Page 'Services'");
        $manager->persist($servicesMetatag);
        // Création d'un objet Metatag pour la page 'Articles' du site
        $articlesMetatag = new Metatag;
        $articlesMetatag
            ->setPageName('Articles')
            ->setTitle("Nos dernières publications'")
            ->setMetaDescription("Page 'Articles'");
        $manager->persist($articlesMetatag);
        // Création d'un objet Metatag pour la page 'Contact' du site
        $contactMetatag = new Metatag;
        $contactMetatag
            ->setPageName('Contact')
            ->setTitle("Nous contacter'")
            ->setMetaDescription("Page 'Contact'");
        $manager->persist($contactMetatag);
        // Création d'un objet Metatag pour la page 'Mentions légales' du site
        $legalNoticeMetatag = new Metatag;
        $legalNoticeMetatag
            ->setPageName('Mentions légales')
            ->setTitle("Mentions légales")
            ->setMetaDescription("Page 'Mentions légales'");
        $manager->persist($legalNoticeMetatag);
        // Envoi en base de données
        $manager->flush();
    }
}
