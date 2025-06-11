<?php

class Composer {
    private $db;
    private $insert;
    private $selectByCommande;

    public function __construct($db){
        $this->db = $db;

        $this->insert = $db->prepare(
            "INSERT INTO composer(idCommande, idProduit, quantite)
             VALUES(:idCommande, :idProduit, :quantite)"
        );

        $this->selectByCommande = $db->prepare(
            "SELECT p.designation, p.prix, c.quantite
             FROM composer c
             JOIN produit p ON p.id = c.idProduit
             WHERE c.idCommande = :idCommande"
        );
    }

    public function insert($idCommande, $idProduit, $quantite){
        $r = true;
        $this->insert->execute([
            ':idCommande' => $idCommande,
            ':idProduit'  => $idProduit,
            ':quantite'   => $quantite
        ]);

        if ($this->insert->errorCode() != 0){
            print_r($this->insert->errorInfo());
            $r = false;
        }

        return $r;
    }

    public function selectByCommande($idCommande){
        $this->selectByCommande->execute([':idCommande' => $idCommande]);
        return $this->selectByCommande->fetchAll();
    }
}
