<?php

class Produit {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function insert($designation, $description, $prix, $image, $categorie) {
        $sql = "INSERT INTO produit (designation, description, prix, image, categorie)
                VALUES (:designation, :description, :prix, :image, :categorie)";
        $query = $this->db->prepare($sql);
        $query->bindValue(':designation', $designation, PDO::PARAM_STR);
        $query->bindValue(':description', $description, PDO::PARAM_STR);
        $query->bindValue(':prix', $prix, PDO::PARAM_STR);
        $query->bindValue(':image', $image, PDO::PARAM_STR);
        $query->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        return $query->execute();
    }

    public function selectAll() {
        $sql = "SELECT * FROM produit";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function selectByCategorie($categorie, $limite = null) {
        $sql = "SELECT * FROM produit WHERE categorie = :categorie";
        if ($limite !== null) {
            $sql .= " LIMIT :limite";
        }

        $query = $this->db->prepare($sql);
        $query->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        if ($limite !== null) {
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        $query->execute();
        return $query->fetchAll();
    }

    public function selectLimite($limite) {
        $sql = "SELECT * FROM produit ORDER BY RAND() LIMIT :limite";
        $query = $this->db->prepare($sql);
        $query->bindValue(':limite', $limite, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    public function selectById($id) {
        $sql = "SELECT * FROM produit WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch();
    }

    public function delete($id) {
        $sql = "DELETE FROM produit WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function update($id, $designation, $description, $prix, $image, $categorie) {
        $sql = "UPDATE produit SET designation = :designation, description = :description,
                prix = :prix, image = :image, categorie = :categorie WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->bindValue(':designation', $designation, PDO::PARAM_STR);
        $query->bindValue(':description', $description, PDO::PARAM_STR);
        $query->bindValue(':prix', $prix, PDO::PARAM_STR);
        $query->bindValue(':image', $image, PDO::PARAM_STR);
        $query->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function getTaillesDisponibles($idProduit) {
    $sql = "SELECT tailles.id, tailles.nom 
            FROM produit_tailles
            INNER JOIN tailles ON produit_tailles.id_taille = tailles.id
            WHERE produit_tailles.id_produit = :idProduit";
    $query = $this->db->prepare($sql);
    $query->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC); // on veut id + nom
}

public function getNomTaille($idTaille) {
    $sql = "SELECT nom FROM tailles WHERE id = :id";
    $query = $this->db->prepare($sql);
    $query->bindValue(':id', $idTaille, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch();
    return $result ? $result['nom'] : null;
}

}