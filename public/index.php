<?php 
session_start(); // Démarre la session utilisateur

require_once '../config/parametres.php';      // Paramètres de connexion BDD
require_once '../config/connexion.php';       // Connexion PDO
require_once '../config/routes.php';          // Liste des routes sécurisées
require_once '../_classes.php';               // Inclus tous les modèles
require_once '../_controleurs.php';           // Inclus tous les contrôleurs

require_once '../lib/vendor/autoload.php';    // Autoload de Composer (Twig, etc.)

// Initialisation de Twig
$loader = new \Twig\Loader\FilesystemLoader('../src/vue');
$twig = new \Twig\Environment($loader, []);

// ✅ Injection de la session dans Twig
$twig->addGlobal('session', $_SESSION); 

// Récupération de la page demandée
$contenu = getPage($db);

// Appel dynamique du contrôleur correspondant
$contenu($twig, $db);
?>