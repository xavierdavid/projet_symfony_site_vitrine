<?php 

namespace App\Services;

/**
 * Représente les filtres de recherche des objets Article
 */
class SearchArticle
{
  /**
   * Propriété de filtre par mot clé
   *
   * @var string
   */
  public $string="";

  /**
   * Propriété de filtre par catégorie
   *
   * @var array
   */
  public $category = [];
}