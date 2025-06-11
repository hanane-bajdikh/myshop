<?php
require_once(__DIR__ . '/../modele/class_produit.php');

function ensemblesControleur($twig, $db) {
    $produit = new Produit($db);
    $ensembles = $produit->selectByCategorie('ensembles');

    echo $twig->render('ensembles.html.twig', ['produits' => $ensembles]);
}
