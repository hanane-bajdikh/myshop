<?php

class Upload {
    private $repertoire;
    private $tailleMax;
    private $extensions;
    private $message;

    public function __construct($repertoire = "public/images/", $tailleMax = 2097152, $extensions = ["jpg", "jpeg", "png", "webp"]){
        $this->repertoire = $repertoire;
        $this->tailleMax = $tailleMax;
        $this->extensions = $extensions;
        $this->message = "";
    }

    public function getMessage() {
        return $this->message;
    }

    public function uploadFichier($fichier){
        $nomFichier = basename($fichier['name']);
        $extension = strtolower(pathinfo($nomFichier, PATHINFO_EXTENSION));

        if (!in_array($extension, $this->extensions)) {
            $this->message = "Extension non autorisée.";
            return false;
        }

        if ($fichier['size'] > $this->tailleMax) {
            $this->message = "Fichier trop volumineux (max : " . ($this->tailleMax / 1048576) . " Mo).";
            return false;
        }

        $nomUnique = uniqid() . "." . $extension;
        $chemin = $this->repertoire . $nomUnique;

        if (!move_uploaded_file($fichier['tmp_name'], $chemin)) {
            $this->message = "Erreur lors de l’envoi du fichier.";
            return false;
        }

        return $nomUnique;
    }
}
