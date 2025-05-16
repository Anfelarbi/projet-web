<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get categories for the dropdown
$stmt = $db->query("SELECT * FROM categories ORDER BY nom");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $nom_fr = $_POST['nom_fr'] ?? '';
    $nom_ar = $_POST['nom_ar'] ?? '';
    $description_fr = $_POST['description_fr'] ?? '';
    $description_ar = $_POST['description_ar'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $categorie_id = $_POST['categorie_id'] ?? null;
    $stock = $_POST['stock'] ?? 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Validate data
    $errors = [];
    
    if (empty($nom_fr)) {
        $errors[] = 'Le nom en français est requis';
    }
    
    if (!is_numeric($prix) || $prix <= 0) {
        $errors[] = 'Le prix doit être un nombre positif';
    }
    
    if (!is_numeric($stock) || $stock < 0) {
        $errors[] = 'Le stock doit être un nombre positif ou zéro';
    }
    
    if (empty($categorie_id)) {
        $errors[] = 'La catégorie est requise';
    }
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../img/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = basename($_FILES['image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $uniqueName = uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $uniqueName;
        
        // Check file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés';
        }
        
        // Check file size (max 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'L\'image ne doit pas dépasser 5MB';
        }
        
        // Upload file if no errors
        if (empty($errors)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = 'products/' . $uniqueName; // Relative path to store in DB
            } else {
                $errors[] = 'Échec de l\'upload de l\'image';
            }
        }
    } else {
        $errors[] = 'Une image est requise';
    }
    
    // Insert data if no errors
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO produits (nom_fr, nom_ar, description_fr, description_ar, prix, image, categorie_id, stock, is_featured) 
                                  VALUES (:nom_fr, :nom_ar, :description_fr, :description_ar, :prix, :image, :categorie_id, :stock, :is_featured)");
            
            $stmt->bindParam(':nom_fr', $nom_fr);
            $stmt->bindParam(':nom_ar', $nom_ar);
            $stmt->bindParam(':description_fr', $description_fr);
            $stmt->bindParam(':description_ar', $description_ar);
            $stmt->bindParam(':prix', $prix);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':categorie_id', $categorie_id);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':is_featured', $is_featured);
            
            $stmt->execute();
            
            // Redirect to products page with success message
            $_SESSION['success_message'] = 'Produit ajouté avec succès';
            header('Location: products.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erreur de base de données: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="../img/logo aura.png">
    <title>Aura Deco | Ajouter un Produit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="admin-logo">
                    <img src="../img/AURA (2).png" alt="Aura Deco Logo" width="40">
                    <span>Aura Admin</span>
                </a>
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="nav-divider"></div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link active">
                        <span class="nav-icon"><i class="fas fa-couch"></i></span>
                        <span class="nav-text">Produits</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-tag"></i></span>
                        <span class="nav-text">Catégories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
                        <span class="nav-text">Commandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customers.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">Clients</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="messages.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-envelope"></i></span>
                        <span class="nav-text">Messages</span>
                    </a>
                </li>
            </ul>
            
            <div class="nav-divider"></div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-cog"></i></span>
                        <span class="nav-text">Paramètres</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span class="nav-text">Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="page-title">Ajouter un Produit</h1>
                
                <div class="admin-actions">
                    <a href="products.php" class="btn-action secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            
            <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="margin-bottom: 0;">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="form-section">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="nom_fr">Nom (Français) *</label>
                                <input type="text" id="nom_fr" name="nom_fr" value="<?php echo isset($_POST['nom_fr']) ? htmlspecialchars($_POST['nom_fr']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nom_ar">Nom (Arabe)</label>
                                <input type="text" id="nom_ar" name="nom_ar" value="<?php echo isset($_POST['nom_ar']) ? htmlspecialchars($_POST['nom_ar']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="prix">Prix (DH) *</label>
                                <input type="number" id="prix" name="prix" min="0" step="0.01" value="<?php echo isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="categorie_id">Catégorie *</label>
                                <select id="categorie_id" name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['nom']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock *</label>
                                <input type="number" id="stock" name="stock" min="0" value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group checkbox">
                                <input type="checkbox" id="is_featured" name="is_featured" <?php echo (isset($_POST['is_featured'])) ? 'checked' : ''; ?>>
                                <label for="is_featured">Produit en vedette</label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label for="description_fr">Description (Français)</label>
                                <textarea id="description_fr" name="description_fr"><?php echo isset($_POST['description_fr']) ? htmlspecialchars($_POST['description_fr']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="description_ar">Description (Arabe)</label>
                                <textarea id="description_ar" name="description_ar"><?php echo isset($_POST['description_ar']) ? htmlspecialchars($_POST['description_ar']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group file">
                                <label for="image">Image *</label>
                                <input type="file" id="image" name="image" accept="image/*" data-preview="image-preview" required>
                                <div id="image-preview" class="image-preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-action">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="products.php" class="btn-action secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>