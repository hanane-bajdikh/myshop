<?php
require_once __DIR__ . '/../modele/class_produit.php';


function ficheProduitControleur($twig, $db) {
    $produit = new Produit($db);
    $id = $_GET['id'] ?? null;

    if ($id && is_numeric($id)) {
        $fiche = $produit->selectById($id);
        $tailles = $produit->getTaillesDisponibles($id);

        if ($fiche) {
            echo $twig->render('ficheproduit.html.twig', [
                'produit' => $fiche,
                'tailles' => $tailles
            ]);
        } else {
            echo $twig->render('erreur.html.twig', ['message' => 'Produit introuvable.']);
        }
    } else {
        echo $twig->render('erreur.html.twig', ['message' => 'Identifiant produit manquant ou invalide.']);
    }
}
