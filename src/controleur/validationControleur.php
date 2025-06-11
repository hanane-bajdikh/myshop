<?php

function validationControleur($twig, $db) {
    $form = [];

    if (isset($_GET['email']) && isset($_GET['idgenere'])) {
        $email = $_GET['email'];
        $idgenere = $_GET['idgenere'];

        $utilisateur = new Utilisateur($db);
        $unUtilisateur = $utilisateur->selectByEmail($email);

        if ($unUtilisateur && $unUtilisateur['idgenere'] == $idgenere) {
            $exec = $utilisateur->validerCompte($email);

            if ($exec) {
                $form['valide'] = true;
                $form['message'] = 'Votre compte a été validé. Vous pouvez vous connecter.';
            } else {
                $form['valide'] = false;
                $form['message'] = 'Erreur lors de la validation.';
            }
        } else {
            $form['valide'] = false;
            $form['message'] = 'Lien invalide ou utilisateur inconnu.';
        }
    } else {
        $form['valide'] = false;
        $form['message'] = 'Paramètres manquants.';
    }

    echo $twig->render('validation.html.twig', ['form' => $form]);
}
