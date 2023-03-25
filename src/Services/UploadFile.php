<?php 

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile
{
  private $uploadedFile;
  private $targetDirectory;

  public function __construct(UploadedFile $uploadedFile, $targetDirectory) {
    $this->uploadedFile = $uploadedFile;
    $this->targetDirectory = $targetDirectory;
  }

  /**
   * Permet d'uploader des fichiers
   *
   * @return void
   */
  public function upload()
  {
    // Récupération du nom original du fichier
    $originalName = pathinfo($this->uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    // Renommage du fichier et affectation de l'extension
    $fileName = $originalName.md5(uniqid()).'.'.$this->uploadedFile->guessExtension();
    // Déplacement du fichier dans le répertoire de stockage
    try {
      $this->uploadedFile->move($this->getTargetDirectory(),$fileName);
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