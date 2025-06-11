<?php

require_once(__DIR__ . '/../modele/class_produit.php');

function ficheProduitControleur($twig, $db) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $produit = new Produit($db);
        $dataProduit = $produit->selectById($id);
        $tailles = $produit->getTaillesDisponibles($id);

        echo $twig->render('ficheproduit.html.twig', [
            'produit' => $dataProduit,
            'tailles' => $tailles
        ]);
    } else {
        // redirection en cas d'erreur ou d'absence d'id
        header("Location: index.php?page=accueil");
        exit();
    }
}
