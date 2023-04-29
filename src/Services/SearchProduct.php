<?php 

namespace App\Services;

/**
 * Représente les filtres de recherche des objets Product
 */
class SearchProduct
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