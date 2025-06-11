<?php
function paiementControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['login'])) {
        header("Location: index.php?page=connexion");
        exit;
    }

    if (empty($_SESSION['panier'])) {
        header("Location: index.php?page=panier");
        exit;
    }

    $utilisateurModel = new Utilisateur($db);
    $produitModel = new Produit($db);

    $utilisateur = $utilisateurModel->selectByEmail($_SESSION['login']);
    $liste = [];
    $total = 0;

    foreach ($_SESSION['panier'] as $item) {
        $produit = $produitModel->selectById($item['id_produit']);
        if ($produit) {
            $produit['quantite'] = $item['quantite'];
            $produit['taille'] = $item['taille'];
            $produit['total'] = $produit['prix'] * $item['quantite'];
            $total += $produit['total'];
            $liste[] = $produit;
        }
    }

    echo $twig->render('paiement.html.twig', [
        'utilisateur' => $utilisateur,
        'liste' => $liste,
        'total' => $total
    ]);
}
