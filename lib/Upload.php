<?php


if (!class_exists('Upload')) {
class Upload {
    private $destinationPath;
    private $allowedExtensions;
    private $maxFileSize;
    private $error;

    public function __construct($destinationPath = 'public/images/', $maxFileSize = 5242880, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']) {
        $this->destinationPath = rtrim($destinationPath, '/') . '/';
        $this->maxFileSize = $maxFileSize;
        $this->allowedExtensions = $allowedExtensions;
    }

    public function uploadFile($file) {
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->error = "Paramètre invalide.";
            return false;
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->error = "Aucun fichier envoyé.";
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->error = "Fichier dépassant la taille limite.";
                return false;
            default:
                $this->error = "Erreur inconnue.";
                return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $this->error = "Le fichier dépasse la taille maximale autorisée.";
            return false;
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $this->allowedExtensions)) {
            $this->error = "Extension de fichier non autorisée.";
            return false;
        }

        $newFileName = uniqid() . '.' . $extension;
        $destination = $this->destinationPath . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->error = "Échec du déplacement du fichier téléchargé.";
            return false;
        }

        return $newFileName;
    }

    public function getError() {
        return $this->error;
    }
}
}