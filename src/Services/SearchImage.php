<?php 

namespace App\Services;

/**
 * Représente les filtres de recherche des objets Image
 */
class SearchImage
{
  /**
   * Propriété de filtre par mot clé
   *
   * @var string
   */
  public $string="";

  /**
   * Propriété de filtre par ordre de priorité
   *
   * @var integer
   */
  public $priorityOrder;
}