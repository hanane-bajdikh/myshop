<?php

function utilisateurModifControleur($twig, $db) {
    $form = [];
    $utilisateur = new Utilisateur($db);

    if (isset($_GET['id'])) {
        $unUtilisateur = $utilisateur->selectById($_GET['id']);

        if ($unUtilisateur) {
            $form['utilisateur'] = $unUtilisateur;
        } else {
            $form['message'] = 'Utilisateur introuvable';
        }
    }

    if (isset($_POST['btModifier'])) {
        $id = $_POST['id'];
        $email = $_POST['email'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $role = $_POST['role'];
        $valider = isset($_POST['valider']) ? 1 : 0;

        $exec = $utilisateur->update($id, $email, $nom, $prenom, $role, $valider);
        if ($exec) {
            $form['valide'] = true;
            $form['message'] = 'Modification réussie';
            $form['utilisateur'] = $utilisateur->selectById($id);
        } else {
            $form['valide'] = false;
            $form['message'] = 'Erreur lors de la modification';
        }
    }

    echo $twig->render('utilisateur-modif.html.twig', ['form' => $form]);
}
