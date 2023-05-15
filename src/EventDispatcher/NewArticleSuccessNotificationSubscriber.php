<?php 

namespace App\EventDispatcher;

use App\Services\SendEmail;
use App\Event\NewArticleSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class NewArticleSuccessNotificationSubscriber implements EventSubscriberInterface
{
  
  private $sendEmail;

  public function __construct(SendEmail $sendEmail)
  {
    $this->sendEmail = $sendEmail;
  }

  /**
   * Retourne un tableau d'événements
   *
   * @return void
   */
  public static function getSubscribedEvents()
  {
    return [
      // L'événement 'new.article.success' déclenche l'appel de la méthode 'sendSuccessEmailNotification'
      'new.article.success' => 'sendSuccessEmailNotification'
    ];
  }
  
  /**
   * Envoie un email de notification à l'administrateur du site
   *
   * @param NewArticleSuccessEvent $newArticleSuccessEvent
   * @return void
   */
  public function sendSuccessEmailNotification(NewArticleSuccessEvent $newArticleSuccessEvent)
  {
    // Récupération de l'objet Article créé
    $article = $newArticleSuccessEvent->getArticle();
    // Paramétrage de l'email de notification
    $from = "contact@xavier-david.com";
    $to = "contact@xavier-david.com";
    $subject = "Création d'un nouvel article sur votre site";
    $htmlTemplate = "email/new_article.html.twig";
    $context = ['article' => $article];
    // Envoi de l'email
    try {
        // Test d'envoi
        $this->sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
    } catch (TransportExceptionInterface $e) {
        // Message d'erreur
        echo "Une erreur est survenue : ". $e->getMessage();
    }
  }
}