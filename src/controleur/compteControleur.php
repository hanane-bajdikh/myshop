<?php
function compteControleur($twig, $db) {
    if (!isset($_SESSION['login'])) {
        header('Location: index.php?page=connexion');
        exit;
    }

    $form = [];
    $utilisateurModel = new Utilisateur($db);
    $commandeModel = new Commande($db);

    // Récupérer les infos de l'utilisateur
    $utilisateur = $utilisateurModel->selectByEmail($_SESSION['login']);

    // Traitement du formulaire de mise à jour
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_infos'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $ancienMdp = $_POST['ancien_mdp'] ?? null;
        $nouveauMdp = $_POST['nouveau_mdp'] ?? null;

        $maj = $utilisateurModel->updateInfos($utilisateur['id'], $nom, $prenom, $email);

        if (!empty($ancienMdp) && !empty($nouveauMdp)) {
            if (password_verify($ancienMdp, $utilisateur['mdp'])) {
                $utilisateurModel->updatePassword($utilisateur['id'], password_hash($nouveauMdp, PASSWORD_DEFAULT));
                $form['message'] = "Informations et mot de passe mis à jour.";
                $form['valide'] = true;
            } else {
                $form['message'] = "Mot de passe actuel incorrect.";
                $form['valide'] = false;
            }
        } else {
            $form['message'] = "Informations mises à jour.";
            $form['valide'] = true;
        }

        $_SESSION['login'] = $email;
        $utilisateur = $utilisateurModel->selectByEmail($email); // Mise à jour
    }

    // Récupération des commandes
    $commandes = $commandeModel->selectByUserId($utilisateur['id']);

    echo $twig->render('compte.html.twig', [
        'utilisateur' => $utilisateur,
        'commandes' => $commandes,
        'form' => $form
    ]);
}
