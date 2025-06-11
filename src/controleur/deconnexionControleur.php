<?php

function deconnexionControleur($twig, $db){
    session_unset();    // Supprime toutes les variables de session
    session_destroy();  // Détruit la session

    header("Location: index.php?page=accueil");
    exit;
}
