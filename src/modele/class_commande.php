<?php

class Commande {
    private $db;
    private $insert;
    private $selectByDateCli;
    private $selectByUtilisateur;

    public function __construct($db){
        $this->db = $db;

        $this->insert = $db->prepare(
            "INSERT INTO commande(montant, date, etat, idUtilisateur)
             VALUES(:montant, :date, :etat, :idUtilisateur)"
        );

        $this->selectByDateCli = $db->prepare(
            "SELECT * FROM commande
             WHERE date = :date AND idUtilisateur = :idUtilisateur"
        );

        $this->selectByUtilisateur = $db->prepare(
            "SELECT * FROM commande
             WHERE idUtilisateur = :idUtilisateur
             ORDER BY date DESC"
        );
    }

    public function insert($montant, $date, $etat, $idUtilisateur){
        $r = true;
        $this->insert->execute([
            ':montant'      => $montant,
            ':date'         => $date,
            ':etat'         => $etat,
            ':idUtilisateur'=> $idUtilisateur
        ]);

        if ($this->insert->errorCode() != 0){
            print_r($this->insert->errorInfo());
            $r = false;
        }

        return $r;
    }

    public function selectByDateCli($date, $idUtilisateur){
        $this->selectByDateCli->execute([
            ':date' => $date,
            ':idUtilisateur' => $idUtilisateur
        ]);
        return $this->selectByDateCli->fetch();
    }

    public function selectByUtilisateur($idUtilisateur) {
    $sql = "SELECT * FROM commande WHERE idUtilisateur = :id ORDER BY date DESC";
    $query = $this->db->prepare($sql);
    $query->bindValue(':id', $idUtilisateur, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


    public function selectByClientId($idClient) {
    $query = "SELECT * FROM commande WHERE idUtilisateur = :id ORDER BY date DESC";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':id', $idClient, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function selectByUserId($id) {
    try {
        $sql = "SELECT * FROM commande WHERE idUtilisateur = :id ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur dans selectByUserId : " . $e->getMessage();
        return [];
    }
}



}
