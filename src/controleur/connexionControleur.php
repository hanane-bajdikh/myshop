<?php
require_once __DIR__ . '/../modele/class_utilisateur.php';

function connexionControleur($twig, $db) {
    $form = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $mdp = $_POST['mdp'] ?? '';
        
        if ($email && $mdp) {
            $utilisateur = new Utilisateur($db);
            $unUser = $utilisateur->selectByEmail($email);
            
            if ($unUser && password_verify($mdp, $unUser['mdp'])) {
                // ✅ Définir TOUTES les variables de session AVANT la redirection
                $_SESSION['login'] = $unUser['email'];
                $_SESSION['user_id'] = $unUser['id'];
                $_SESSION['user_nom'] = $unUser['nom'];
                $_SESSION['user_prenom'] = $unUser['prenom'];
                $_SESSION['connecte'] = true;
                
                // 🔑 Vérification du rôle admin
                if ($email === 'hanane@shop.com' || $unUser['idRole'] == 1) {
                    $_SESSION['role'] = 1;
                    $_SESSION['admin'] = true;  // ✅ Défini AVANT la redirection
                    
                    header('Location: index.php?page=admin');
                    exit;
                } else {
                    $_SESSION['role'] = 2;
                    
                    if (!empty($_SESSION['panier'])) {
                        header('Location: index.php?page=panier');
                    } else {
                        header('Location: index.php?page=compte');
                    }
                    exit;
                }
                
            } else {
                $form['valide'] = false;
                $form['message'] = 'Email ou mot de passe incorrect';
            }
        } else {
            $form['valide'] = false;
            $form['message'] = 'Veuillez remplir tous les champs';
        }
    }
    
    echo $twig->render('connexion.html.twig', ['form' => $form]);
}
?>