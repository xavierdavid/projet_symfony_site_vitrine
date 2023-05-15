<?php 

namespace App\Event;

use App\Entity\Article;
use Symfony\Contracts\EventDispatcher\Event;

class NewArticleSuccessEvent extends Event
{
  private $article;

  public function __construct(Article $article)
  {
    $this->article = $article;
  }

  /**
   * Permet de retourner l'objet Article créé
   *
   * @return Article
   */
  public function getArticle(): Article
  {
    return $this->article;
  }
}