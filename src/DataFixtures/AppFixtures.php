<?php

namespace App\DataFixtures;

use App\Entity\User;
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
        // Création de données pour l'administrateur du site
        $admin = new User;
        // Hashage du mot de passe
        $hashPassword = $this->userPasswordHasherInterface->hashPassword($admin, 'password');
        $admin  ->setEmail("admin@gmail.com")
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hashPassword)
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
                $manager->persist($admin);
            
        $manager->flush();
    }
}
