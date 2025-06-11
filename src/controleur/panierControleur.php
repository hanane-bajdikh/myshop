<?php

require_once __DIR__ . '/../modele/class_produit.php';
require_once __DIR__ . '/../modele/class_utilisateur.php';
require_once __DIR__ . '/../modele/class_commande.php';
require_once __DIR__ . '/../modele/class_composer.php';

function panierControleur($twig, $db) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $form = [];
    $liste = [];

    // 1. Valider la commande
    if (isset($_POST['placerCommande'])) {
        if (!isset($_SESSION['login'])) {
            $form['valide'] = false;
            $form['message'] = "Veuillez vous connecter pour finaliser la commande.";
        } elseif (empty($_SESSION['panier'])) {
            $form['valide'] = false;
            $form['message'] = "Votre panier est vide.";
        } else {
            $montant = $_POST['montant'];
            $date = (new DateTime('now', new DateTimeZone('Europe/Paris')))->format("Y-m-d H:i:s");
            $etat = 1;

            $utilisateur = new Utilisateur($db);
            $unUtil = $utilisateur->selectByEmail($_SESSION['login']);
            $idUtilisateur = $unUtil['id'];

            $commande = new Commande($db);
            $exec = $commande->insert($montant, $date, $etat, $idUtilisateur);

            if (!$exec) {
                $form['valide'] = false;
                $form['message'] = "Problème lors de l'enregistrement de la commande.";
            } else {
                $form['valide'] = true;
                $form['message'] = "Commande enregistrée avec succès.";
                $maCommande = $commande->selectByDateCli($date, $idUtilisateur);

                $composer = new Composer($db);
                foreach ($_SESSION['panier'] as $item) {
                    $composer->insert($maCommande['id'], $item['id_produit'], $item['quantite'], $item['taille']); // Ajoute la taille
                }

                unset($_SESSION['panier']); // Vider le panier
            }
        }
    }

    // 2. Supprimer un article
    if (isset($_GET['remove'])) {
        $cle = $_GET['remove'];
        if (isset($_SESSION['panier'][$cle])) {
            unset($_SESSION['panier'][$cle]);
        }
    }

    // 3. Mise à jour des quantités
    if (isset($_POST['update'])) {
        foreach ($_POST as $k => $v) {
            if (preg_match('/^q-(\d+)-(.+)$/', $k, $matches)) {
                $id = $matches[1];
                $taille = $matches[2];
                foreach ($_SESSION['panier'] as &$item) {
                    if ($item['id_produit'] == $id && $item['taille'] == $taille) {
                        $item['quantite'] = max(1, (int)$v);
                        break;
                    }
                }
                unset($item);
            }
        }
        header("Location: index.php?page=panier");
        exit;
    }

    // 4. Affichage des produits du panier
    if (!empty($_SESSION['panier'])) {
        $produitModel = new Produit($db);
        foreach ($_SESSION['panier'] as $index => $item) {
            $produit = $produitModel->selectById($item['id_produit']);
            if ($produit) {
                $produit['quantite'] = $item['quantite'];
                $produit['taille'] = $produitModel->getNomTaille($item['taille']);
                $produit['cle'] = $index;
                $liste[] = $produit;
            }
        }
    }

    echo $twig->render('panier.html.twig', [
        'form' => $form,
        'liste' => $liste
    ]);
}
