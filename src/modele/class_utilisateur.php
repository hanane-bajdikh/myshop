<?php

class Utilisateur {
    private $db;
    private $insert;
    private $selectByEmail;

    public function __construct($db) {
        $this->db = $db;

        // Requête pour insérer un nouvel utilisateur
        $this->insert = $db->prepare("INSERT INTO utilisateur(email, mdp, idRole, nom, prenom, valider, idgenere)
                                      VALUES (:email, :mdp, :role, :nom, :prenom, :valider, :idgenere)");

        // Requête pour chercher un utilisateur par email
        $this->selectByEmail = $db->prepare("SELECT * FROM utilisateur WHERE email = :email");
    }

    // Insertion d’un nouvel utilisateur
    public function insert($email, $mdp, $role, $nom, $prenom) {
        $valider = false;
        $idgenere = uniqid(); // Chaîne unique pour validation e-mail
        $r = true;

        $this->insert->execute([
            ':email'    => $email,
            ':mdp'      => $mdp,
            ':role'     => $role,
            ':nom'      => $nom,
            ':prenom'   => $prenom,
            ':valider'  => $valider,
            ':idgenere' => $idgenere
        ]);

        if ($this->insert->errorCode() != 0) {
            print_r($this->insert->errorInfo());
            $r = false;
        }

        return $r;
    }

    // Recherche d’un utilisateur par email
    public function selectByEmail($email) {
        $this->selectByEmail->execute([':email' => $email]);

        if ($this->selectByEmail->errorCode() != 0) {
            print_r($this->selectByEmail->errorInfo());
        }

        return $this->selectByEmail->fetch();
    }

    public function updateInfos($id, $nom, $prenom, $email) {
    $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':nom', $nom);
    $stmt->bindValue(':prenom', $prenom);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}

public function updatePassword($id, $nouveauHash) {
    $sql = "UPDATE utilisateur SET mdp = :mdp WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':mdp', $nouveauHash);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}

}
