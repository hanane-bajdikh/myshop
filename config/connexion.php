<?php
require_once 'parametres.php';

try {
    $db = new PDO(
        'mysql:host='.$config['serveur'].';dbname='.$config['bd'].';charset=utf8',
        $config['login'],
        $config['mdp']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    $db = null;
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
}
