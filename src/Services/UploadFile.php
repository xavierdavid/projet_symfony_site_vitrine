<?php 

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile
{
  private $targetDirectory;

  public function __construct($targetDirectory) 
  {
    $this->targetDirectory = $targetDirectory;
  }

  /**
   * Permet d'uploader des fichiers
   *
   * @return void
   */
  public function upload(UploadedFile $uploadedFile)
  {
    // Récupération du nom original du fichier
    $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    // Renommage du fichier et affectation de l'extension
    $fileName = $originalName.md5(uniqid()).'.'.$uploadedFile->guessExtension();
    // Déplacement du fichier dans le répertoire de stockage
    try {
      $uploadedFile->move($this->getTargetDirectory(),$fileName);
    } catch (FileException $e) {
    
    }
    return $fileName;
  }

  /**
   * Permet de retourner le nom du répertoire cible pour l'upload de fichiers
   *
   * @return string
   */
  public function getTargetDirectory(): string
  {
    return $this->targetDirectory;
  }
}