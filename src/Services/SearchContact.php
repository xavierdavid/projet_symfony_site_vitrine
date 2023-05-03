<?php 

namespace App\Services;

/**
 * Représente les filtres de recherche des objets Contact
 */
class SearchContact
{
  /**
   * Propriété de filtre par mot clé
   *
   * @var string
   */
  public $string="";

  /**
   * Propriété de filtre par expéditeur
   *
   * @var string
   */
  public $email;
}