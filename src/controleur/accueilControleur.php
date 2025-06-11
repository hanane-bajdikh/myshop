<?php

function accueilControleur($twig, $db) {
    $produit = new Produit($db);

    // Exemple : récupérer 6 produits aléatoires ou populaires
    $coupsDeCoeur = $produit->selectLimite(6);

    echo $twig->render('accueil.html.twig', [
        'produits' => $coupsDeCoeur
    ]);
}
