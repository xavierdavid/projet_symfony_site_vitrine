<?php 

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class SubscribeToMailJet {
  
  // Clés d'API MailJet
  private $mailjet_api_key;
  private $mailjet_api_key_secret;

  public function __construct($mailjet_api_key, $mailjet_api_key_secret)
  {
    $this->mailjet_api_key=$mailjet_api_key;
    $this->mailjet_api_key_secret=$mailjet_api_key_secret;
  }

  /**
   * Permet d'envoyer de nouveaux contact 'emails' vers le service tiers MailJet
   *
   * @param [type] $email
   * @return void
   */
  public function addNewContact($email)
  {
    // Instanciation d'un nouvel objet MailJet
    $mj = new Client($this->mailjet_api_key, $this->mailjet_api_key_secret);
    // Ajout d'un nouveau contact 'email' au compte MailJet
    $body = [
      'email'=> $email
    ];
    // Traitement de la requête 'POST' et de la réponse
    $response = $mj->post(Resources::$Contact, ['body' => $body]);
    $response->success();
  }
}