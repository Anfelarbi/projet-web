<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];

// Get product count
$stmt = $db->query("SELECT COUNT(*) as product_count FROM produits");
$product_count = $stmt->fetch(PDO::FETCH_ASSOC)['product_count'] ?? 0;

// Get order count
$stmt = $db->query("SELECT COUNT(*) as order_count FROM commandes");
$order_count = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'] ?? 0;

// Get total revenue
$stmt = $db->query("SELECT SUM(montant) as total_revenue FROM commandes");
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

// Get customer count
$stmt = $db->query("SELECT COUNT(*) as customer_count FROM clients");
$customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['customer_count'] ?? 0;

// Get recent orders
$stmt = $db->query("SELECT c.id, cl.nom, cl.email, c.date_commande, c.montant, c.statut 
                   FROM commandes c 
                   JOIN clients cl ON c.client_id = cl.id 
                   ORDER BY c.date_commande DESC 
                   LIMIT 5");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top products
$stmt = $db->query("SELECT p.id, p.nom_fr, COUNT(dc.produit_id) as order_count, SUM(dc.quantite) as quantity
                   FROM produits p
                   LEFT JOIN details_commande dc ON p.id = dc.produit_id
                   GROUP BY p.id
                   ORDER BY quantity DESC
                   LIMIT 5");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="../img/logo aura.png">
    <title>Aura Deco | Dashboard</title>
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
                    <a href="dashboard.php" class="nav-link active">
                        <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link">
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
                <h1 class="page-title">Dashboard</h1>
                
                <div class="admin-actions">
                    <div class="admin-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher...">
                    </div>
                    
                    <div class="admin-profile">
                        <button class="profile-toggle">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($admin_username, 0, 1)); ?>
                            </div>
                            <span class="profile-name"><?php echo htmlspecialchars($admin_username); ?></span>
                        </button>
                        
                        <div class="profile-dropdown">
                            <ul class="profile-menu">
                                <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                                <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-icon products">
                        <i class="fas fa-couch"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $product_count; ?></h3>
                        <p>Produits</p>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $order_count; ?></h3>
                        <p>Commandes</p>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon revenue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo number_format($total_revenue, 2); ?> DH</h3>
                        <p>Chiffre d'affaires</p>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon customers">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $customer_count; ?></h3>
                        <p>Clients</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="data-section">
                <div class="section-header">
                    <h2 class="section-title">Commandes Récentes</h2>
                    <div class="section-actions">
                        <a href="orders.php" class="btn-action">
                            <i class="fas fa-eye"></i> Voir tout
                        </a>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr id="order-<?php echo $order['id']; ?>">
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['nom']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></td>
                            <td><?php echo number_format($order['montant'], 2); ?> DH</td>
                            <td>
                                <?php
                                $statusClass = '';
                                switch($order['statut']) {
                                    case 'livré':
                                        $statusClass = 'status-active';
                                        break;
                                    case 'en cours':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'annulé':
                                        $statusClass = 'status-inactive';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($order['statut']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn-table-action" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn-table-action edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($recent_orders) === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Aucune commande trouvée</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top Products -->
            <div class="data-section">
                <div class="section-header">
                    <h2 class="section-title">Produits Populaires</h2>
                    <div class="section-actions">
                        <a href="products.php" class="btn-action">
                            <i class="fas fa-eye"></i> Voir tout
                        </a>
                    </div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produit</th>
                            <th>Commandes</th>
                            <th>Quantité Vendue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $product): ?>
                        <tr id="product-<?php echo $product['id']; ?>">
                            <td>#<?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['nom_fr']); ?></td>
                            <td><?php echo $product['order_count'] ?: 0; ?></td>
                            <td><?php echo $product['quantity'] ?: 0; ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="view_product.php?id=<?php echo $product['id']; ?>" class="btn-table-action" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-table-action edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($top_products) === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun produit trouvé</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>