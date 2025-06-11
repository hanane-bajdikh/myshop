<?php

function rechercheControleur($twig, $db) {
    $form = [];
    $liste = [];

    if (isset($_POST['motcle'])) {
        $motcle = $_POST['motcle'];
        $produit = new Produit($db);
        $liste = $produit->recherche($motcle);

        if (!$liste) {
            $form['valide'] = false;
            $form['message'] = 'Aucun résultat pour : ' . htmlspecialchars($motcle);
        } else {
            $form['valide'] = true;
            $form['message'] = count($liste) . ' produit(s) trouvé(s)';
        }
    }

    echo $twig->render('recherche.html.twig', ['form' => $form, 'liste' => $liste]);
}
