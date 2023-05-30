<?php 

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class SubscribeToMailJet {
  
  // Clés d'API MailJet
  private $api_key = '04a519e07e603468a821100c678095bd';
  private $api_key_secret = '4df7de4a91c09274d6d44a813765979f';

  public function addNewContact($email)
  {
    // Instanciation d'un nouvel objet MailJet
    $mj = new Client($this->api_key, $this->api_key_secret);
    // Ajout d'un nouveau contact 'email' au compte MailJet
    $body = [
      'email'=> $email
    ];
    // Traitement de la requête 'POST' et de la réponse
    $response = $mj->post(Resources::$Contact, ['body' => $body]);
    $response->success();
  }
}