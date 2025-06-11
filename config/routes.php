<?php
function getPage($db) {
    // Déclaration des routes : nom => "controleur;autorisation"
    $lesPages['accueil']         = "accueilControleur;0";
    $lesPages['connexion']       = "connexionControleur;0";
    $lesPages['inscrire']        = "inscrireControleur;0";
    $lesPages['apropos']         = "aproposControleur;0";
    $lesPages['mentions']        = "mentionsControleur;0";
    $lesPages['utilisateur']     = "utilisateurControleur;1";
    $lesPages['produit']         = "produitControleur;1";
    $lesPages['panier']          = "panierControleur;0";
    $lesPages['deconnexion']     = "deconnexionControleur;2";
    $lesPages['validation']      = "validationControleur;0";
    $lesPages['recherche']       = "rechercheControleur;0";
    $lesPages['listeproduitpdf'] = "actionListeProduitPdf;1";
    $lesPages['maintenance']     = "maintenanceControleur;0";
    $lesPages['fiche']           = "ficheProduitControleur;0";
    $lesPages['hauts'] = "hautControleur;0";
    $lesPages['bas'] = "basControleur;0";
    $lesPages['ensembles'] = "ensemblesControleur;0";
    $lesPages['robes'] = "robesControleur;0";
    $lesPages['fiche'] = 'ficheProduitControleur;0';
    $lesPages['fiche_produit'] = 'ficheProduitControleur;0';
    $lesPages['ajouterpanier'] = 'ajouterpanierControleur;0';
    $lesPages['compte'] = 'compteControleur;0';
    $lesPages['paiement'] = 'paiementControleur;0';
    $lesPages['admin'] = 'adminControleur;0';

    // Traitement de la page demandée
    if ($db != null) {
        $page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
        if (!isset($lesPages[$page])) {
            $page = 'accueil';
        }
        $explose = explode(";", $lesPages[$page]);
        $nomControleur = $explose[0];
        $autorisation = $explose[1];

        // Vérification de l'autorisation
        if ($autorisation != 0) {
            if (isset($_SESSION['login'])) {
                // ✅ SUPPRIMEZ le cas spécial pour admin - laissez adminControleur gérer
                if ($autorisation == 1) {
                    // Pour les pages admin (niveau 1)
                    if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
                        $nomControleur = 'accueilControleur';
                    }
                } elseif ($autorisation == 2) {
                    // Pour les autres autorisations
                    if (!isset($_SESSION['role']) || $_SESSION['role'] != $autorisation) {
                        $nomControleur = 'accueilControleur';
                    }
                }
            } else {
                $nomControleur = 'accueilControleur';
            }
        }
    } else {
        $nomControleur = 'maintenanceControleur';
    }

    return $nomControleur;
}