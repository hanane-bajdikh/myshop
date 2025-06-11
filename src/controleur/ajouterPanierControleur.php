<?php
function ajouterpanierControleur($twig, $db) {
    // Démarre la session si ce n'est pas déjà fait
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifie que la requête est bien de type POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupère et valide les données du formulaire
        $idProduit = filter_input(INPUT_POST, 'id_produit', FILTER_VALIDATE_INT);
        $taille = filter_input(INPUT_POST, 'taille', FILTER_SANITIZE_STRING);

        if (!$idProduit || !$taille) {
            header('Location: ?page=erreur&message=Produit ou taille invalide');
            exit;
        }

        // Initialise le panier si vide
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        // Crée une clé unique pour chaque couple produit/taille
        $cle = $idProduit . '-' . $taille;

        // Incrémente la quantité si l'article existe déjà
        if (isset($_SESSION['panier'][$cle])) {
            $_SESSION['panier'][$cle]['quantite'] += 1;
        } else {
            // Sinon, ajoute un nouveau produit au panier
            $_SESSION['panier'][$cle] = [
                'id_produit' => $idProduit,
                'taille' => $taille,
                'quantite' => 1
            ];
        }

        // Redirige vers la page du panier
        header('Location: ?page=panier');
        exit;
    } else {
        // Si accès direct en GET, rediriger vers accueil
        header('Location: index.php');
        exit;
    }
}
