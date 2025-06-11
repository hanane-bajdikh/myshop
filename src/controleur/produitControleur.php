<?php
require_once(__DIR__ . '/../modele/class_produit.php');
require_once(__DIR__ . '/../../lib/Upload.php');

function produitControleur($twig, $db) {
    $form = [];
    $produit = new Produit($db);

    // Ajouter un produit
    if (isset($_POST['btAjouter'])) {
        $designation = filter_input(INPUT_POST, 'designation', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $prix = filter_input(INPUT_POST, 'prix', FILTER_VALIDATE_FLOAT);
        $categorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_STRING);

        if ($designation && $description && $prix !== false && $categorie) {
            $upload = new Upload();
            $imageName = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $imageName = $upload->uploadFile($_FILES['image']);
                if (!$imageName) {
                    $form['valide'] = false;
                    $form['message'] = 'Erreur lors de l\'upload de l\'image : ' . $upload->getError();
                }
            }

            // Si aucune erreur d'upload
            if (!isset($form['valide']) || $form['valide'] !== false) {
                $exec = $produit->insert($designation, $description, $prix, $imageName, $categorie);
                if ($exec) {
                    $form['valide'] = true;
                    $form['message'] = 'Produit ajouté avec succès.';
                } else {
                    $form['valide'] = false;
                    $form['message'] = 'Échec de l\'ajout du produit.';
                }
            }
        } else {
            $form['valide'] = false;
            $form['message'] = 'Veuillez remplir tous les champs correctement.';
        }
    }

    // Supprimer un produit
    if (isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            $exec = $produit->delete($id);
            $form['valide'] = $exec;
            $form['message'] = $exec ? 'Produit supprimé avec succès.' : 'Échec de la suppression.';
        } else {
            $form['valide'] = false;
            $form['message'] = 'ID de produit invalide.';
        }
    }

    // Afficher tous les produits
    $liste = $produit->selectAll();

    echo $twig->render('produit.html.twig', [
        'form' => $form,
        'liste' => $liste
    ]);
}
