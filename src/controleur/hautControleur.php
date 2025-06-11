<?php
require_once(__DIR__ . '/../modele/class_produit.php');


function hautControleur($twig, $db) {
    $produit = new Produit($db);
    $hauts = $produit->selectByCategorie("hauts");
    
    echo $twig->render('hauts.html.twig', [
        'hauts' => $hauts
    ]);
}
