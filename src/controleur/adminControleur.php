<?php
// adminControleur.php - Contrôleur pour l'administration

// Vérifier si l'utilisateur est connecté ET admin
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: index.php?page=connexion');
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: index.php?page=compte'); // Rediriger vers compte client
    exit;
}

// Utiliser $db au lieu de $pdo (cohérent avec votre structure)
$pdo = $db;

// Traitement des actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'ajouter_produit':
                // Gestion upload image
                $imageNom = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadDir = __DIR__ . '/../../public/images/';
                    
                    // Créer le dossier s'il n'existe pas
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Vérifier le type de fichier
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($_FILES['image']['type'], $allowedTypes)) {
                        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $imageNom = uniqid() . '.' . $extension;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageNom)) {
                            // Image uploadée avec succès
                        } else {
                            throw new Exception("Erreur lors de l'upload de l'image");
                        }
                    } else {
                        throw new Exception("Type de fichier non autorisé");
                    }
                }
                
                // Insertion en base
                $sql = "INSERT INTO produit (designation, description, prix, categorie, stock, image) 
                        VALUES (:designation, :description, :prix, :categorie, :stock, :image)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':designation' => trim($_POST['designation']),
                    ':description' => trim($_POST['description']),
                    ':prix' => (float)$_POST['prix'],
                    ':categorie' => $_POST['categorie'],
                    ':stock' => (int)$_POST['stock'],
                    ':image' => $imageNom
                ]);
                
                echo json_encode([
                    'success' => $result, 
                    'message' => $result ? 'Produit ajouté avec succès !' : 'Erreur lors de l\'ajout du produit'
                ]);
                exit;
                
            case 'modifier_produit':
                // Gestion upload image
                $imageNom = $_POST['image_actuelle'] ?? null;
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadDir = __DIR__ . '/../../public/images/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($_FILES['image']['type'], $allowedTypes)) {
                        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $newImageName = uniqid() . '.' . $extension;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImageName)) {
                            // Supprimer l'ancienne image si elle existe
                            if ($imageNom && file_exists($uploadDir . $imageNom)) {
                                unlink($uploadDir . $imageNom);
                            }
                            $imageNom = $newImageName;
                        }
                    }
                }
                
                // Modification en base
                $sql = "UPDATE produit SET 
                        designation = :designation, 
                        description = :description, 
                        prix = :prix, 
                        categorie = :categorie, 
                        stock = :stock, 
                        image = :image 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':id' => (int)$_POST['id'],
                    ':designation' => trim($_POST['designation']),
                    ':description' => trim($_POST['description']),
                    ':prix' => (float)$_POST['prix'],
                    ':categorie' => $_POST['categorie'],
                    ':stock' => (int)$_POST['stock'],
                    ':image' => $imageNom
                ]);
                
                echo json_encode([
                    'success' => $result, 
                    'message' => $result ? 'Produit modifié avec succès !' : 'Erreur lors de la modification'
                ]);
                exit;
                
            case 'supprimer_produit':
                // Récupérer les infos du produit pour supprimer l'image
                $sql = "SELECT image FROM produit WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => (int)$_POST['id']]);
                $produit = $stmt->fetch();
                
                // Supprimer le produit
                $sql = "DELETE FROM produit WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([':id' => (int)$_POST['id']]);
                
                // Supprimer l'image physique si elle existe
                if ($result && $produit && $produit['image']) {
                    $imagePath = __DIR__ . '/../../public/images/' . $produit['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                echo json_encode([
                    'success' => $result, 
                    'message' => $result ? 'Produit supprimé avec succès !' : 'Erreur lors de la suppression'
                ]);
                exit;
                
            case 'changer_statut_commande':
                $sql = "UPDATE commande SET etat = :statut WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':id' => (int)$_POST['id'],
                    ':statut' => (int)$_POST['statut']
                ]);
                
                $statusText = ['En attente', 'Confirmée', 'Expédiée', 'Annulée'];
                $message = $result ? 
                    'Commande #' . $_POST['id'] . ' mise à jour : ' . ($statusText[$_POST['statut']] ?? 'Statut inconnu') :
                    'Erreur lors de la modification du statut';
                
                echo json_encode([
                    'success' => $result, 
                    'message' => $message
                ]);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur : ' . $e->getMessage()
        ]);
        exit;
    }
}

// Récupération des données pour l'affichage
try {
    // === STATISTIQUES ===
    
    // Total produits
    $sql = "SELECT COUNT(*) as total FROM produit";
    $stmt = $pdo->query($sql);
    $totalProduits = $stmt->fetch()['total'];
    
    // Total commandes
    $sql = "SELECT COUNT(*) as total FROM commande";
    $stmt = $pdo->query($sql);
    $totalCommandes = $stmt->fetch()['total'];
    
    // Total utilisateurs (exclure les admins)
    $sql = "SELECT COUNT(*) as total FROM utilisateur WHERE role != 'admin'";
    $stmt = $pdo->query($sql);
    $totalUtilisateurs = $stmt->fetch()['total'];
    
    // Chiffre d'affaires du mois
    $sql = "SELECT COALESCE(SUM(montant), 0) as ca 
            FROM commande 
            WHERE etat = 1 
            AND MONTH(date) = MONTH(CURRENT_DATE()) 
            AND YEAR(date) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->query($sql);
    $caMois = $stmt->fetch()['ca'];
    
    $stats = [
        'total_produits' => $totalProduits,
        'total_commandes' => $totalCommandes,
        'total_utilisateurs' => $totalUtilisateurs,
        'ca_mois' => number_format($caMois, 2, ',', ' ')
    ];
    
    // === PRODUITS ===
    $sql = "SELECT * FROM produit ORDER BY designation";
    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // === COMMANDES RÉCENTES ===
    $sql = "SELECT c.id, c.montant, c.date, c.etat, u.nom, u.prenom, u.email 
            FROM commande c 
            JOIN utilisateur u ON c.idUtilisateur = u.id 
            ORDER BY c.date DESC 
            LIMIT 15";
    $stmt = $pdo->query($sql);
    $commandesRecentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // === UTILISATEURS RÉCENTS ===
    $sql = "SELECT u.id, u.nom, u.prenom, u.email, u.valider, COUNT(c.id) as nb_commandes 
            FROM utilisateur u 
            LEFT JOIN commande c ON u.id = c.idUtilisateur 
            WHERE u.idRole != 1
            GROUP BY u.id, u.nom, u.prenom, u.email, u.valider
            ORDER BY u.id DESC 
            LIMIT 10";    $stmt = $pdo->query($sql);
    $utilisateursRecents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // === PRODUITS EN RUPTURE DE STOCK ===
    $sql = "SELECT id, designation, stock FROM produit WHERE stock <= 5 ORDER BY stock ASC, designation";
    $stmt = $pdo->query($sql);
    $produitsRupture = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $erreur = "Erreur lors du chargement des données : " . $e->getMessage();
    
    // Valeurs par défaut en cas d'erreur
    $stats = [
        'total_produits' => 0,
        'total_commandes' => 0,
        'total_utilisateurs' => 0,
        'ca_mois' => '0,00'
    ];
    $produits = [];
    $commandesRecentes = [];
    $utilisateursRecents = [];
    $produitsRupture = [];
}

// === AFFICHAGE DE LA VUE ===
$titre = "Administration - MyShop";

echo $twig->render('dashboard.html.twig', [
    'titre' => $titre,
    'stats' => $stats,
    'produits' => $produits,
    'commandesRecentes' => $commandesRecentes,
    'utilisateursRecents' => $utilisateursRecents,
    'produitsRupture' => $produitsRupture,
    'erreur' => $erreur ?? null
]);
?>