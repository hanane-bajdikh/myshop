<?php
/**
 * Inclusion de tous les contrôleurs
 * Fichier: _controleurs.php
 */

// Liste des contrôleurs à inclure
$controleurs = [
    'accueilControleur.php',
    'connexionControleur.php',
    'inscrireControleur.php',
    'utilisateurControleur.php',
    'produitControleur.php',
    'panierControleur.php',
    'rechercheControleur.php',
    'deconnexionControleur.php',
    'validationControleur.php',
    'actionListeProduitPdf.php',
    'maintenanceControleur.php',
    'ficheControleur.php',
    'hautControleur.php',
    'basControleur.php',
    'ensemblesControleur.php',
    'robesControleur.php',
    'ajouterPanierControleur.php',
    'compteControleur.php',
    'paiementControleur.php',
    'adminControleur'
    



];

// Répertoire des contrôleurs
$dossierControleur = __DIR__ . '/src/controleur/';

// Inclure chaque contrôleur
foreach ($controleurs as $controleur) {
    $cheminComplet = $dossierControleur . $controleur;
    
    if (file_exists($cheminComplet)) {
        require_once $cheminComplet;
    }
}

function adminControleur($twig, $db) {
    include __DIR__ . '/src/controleur/adminControleur.php';
}
?>