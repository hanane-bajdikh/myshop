<?php
require_once __DIR__ . '/../modele/class_produit.php';

function robesControleur($twig, $db) {
    $produit = new Produit($db);

    // Attention : adapte ici en fonction de ce que tu as dans ta BDD
    $robes = $produit->selectByCategorie("robes"); // ou "robes" si c’est au pluriel dans la BDD

    echo $twig->render('robes.html.twig', ['produits' => $robes]);
}
