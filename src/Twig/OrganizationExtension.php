<?php 

namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\Organization;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Extension Twig permettant de transmettre des données à tous les templates
 */
class OrganizationExtension extends AbstractExtension
{
  private $entityManagerInterface;

  public function __construct(EntityManagerInterface $entityManagerInterface) 
  {
    $this->entityManagerInterface = $entityManagerInterface;
  }

  /**
   * Déclare une nouvelle fonction Twig accessible dans tous les templates du site
   *
   * @return array
   */
  public function getFunctions(): array
  {
    return [
      new TwigFunction('organization', [$this, 'getOrganization'])
    ];
  }

  /**
   * Retourne dans un tableau le dernier objet Organization enregistré en base de données
   *
   * @return void
   */
  public function getOrganization()
  {
    return $this->entityManagerInterface->getRepository(Organization::class)->findBy([], [
      'id' => 'DESC'], // Tri par identifiant et par ordre décroissant
      1, // Limite de 1 enregistrement
      0 // Offset
    );
  }
}