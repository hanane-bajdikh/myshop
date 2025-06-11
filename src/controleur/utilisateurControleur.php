<?php

function utilisateurControleur($twig, $db) {
    $form = [];
    $utilisateur = new Utilisateur($db);

    // Suppression en masse via cases à cocher
    if (isset($_POST['btSupprimer'])) {
        if (isset($_POST['cocher'])) {
            $form['valide'] = true;

            foreach ($_POST['cocher'] as $id) {
                $exec = $utilisateur->delete($id);
                if (!$exec) {
                    $form['valide'] = false;
                    $form['message'] = 'Erreur lors de la suppression de l\'utilisateur ID : ' . $id;
                }
            }

            if ($form['valide']) {
                $form['message'] = 'Suppression réussie';
            }
        } else {
            $form['valide'] = false;
            $form['message'] = 'Aucun utilisateur sélectionné';
        }
    }

    // Récupérer tous les utilisateurs
    $liste = $utilisateur->selectAll();
    echo $twig->render('utilisateur.html.twig', ['form' => $form, 'liste' => $liste]);
}
