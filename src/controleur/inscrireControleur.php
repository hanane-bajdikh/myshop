<?php
function inscrireControleur($twig, $db) {
    $form = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom']);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $mdp = $_POST['mdp'] ?? null;

        if ($nom && $email && $mdp) {
            // Vérifie si l'email existe déjà
            $check = $db->prepare("SELECT * FROM utilisateur WHERE email = :email");
            $check->bindValue(':email', $email);
            $check->execute();

            if ($check->rowCount() > 0) {
                $form['valide'] = false;
                $form['message'] = "Cet e-mail est déjà utilisé.";
            } else {
                $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

                $sql = "INSERT INTO utilisateur (nom, email, motdepasse, role) 
                        VALUES (:nom, :email, :mdp, :role)";
                $query = $db->prepare($sql);
                $query->bindValue(':nom', $nom);
                $query->bindValue(':email', $email);
                $query->bindValue(':mdp', $mdpHash);
                $query->bindValue(':role', 2); // client

                if ($query->execute()) {
                    $form['valide'] = true;
                    $form['message'] = "Inscription réussie.";
                } else {
                    $form['valide'] = false;
                    $form['message'] = "Erreur lors de l'inscription.";
                }
            }
        } else {
            $form['valide'] = false;
            $form['message'] = "Veuillez remplir tous les champs.";
        }
    }

    echo $twig->render('inscrire.html.twig', ['form' => $form]);
}
