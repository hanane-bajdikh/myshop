<?php
require_once(__DIR__ . '/../modele/class_produit.php');

function basControleur($twig, $db) {
    $produit = new Produit($db);
    $listeBas = $produit->selectByCategorie('bas');

    
    echo $twig->render('bas.html.twig', ['listeBas' => $listeBas]);
}