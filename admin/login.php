<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="../img/logo aura.png">
    <title>Aura Deco | Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: var(--color-background-alt);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            background-color: var(--color-background);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-lg);
            padding: var(--spacing-xl);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .login-logo img {
            height: 80px;
            margin: 0 auto;
        }
        
        .login-title {
            text-align: center;
            color: var(--color-primary);
            margin-bottom: var(--spacing-lg);
        }
        
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: var(--spacing-md);
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius-sm);
            font-family: var(--font-body);
        }
        
        .form-group .input-icon {
            position: relative;
        }
        
        .form-group .input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: var(--spacing-md);
            color: var(--color-text-medium);
        }
        
        .form-group .input-icon input {
            padding-left: 40px;
        }
        
        .login-btn {
            background-color: var(--color-primary);
            color: var(--color-text-light);
            border: none;
            padding: var(--spacing-md);
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            margin-top: var(--spacing-md);
            transition: background-color var(--transition-fast);
        }
        
        .login-btn:hover {
            background-color: var(--color-primary-dark);
        }
        
        .login-footer {
            text-align: center;
            margin-top: var(--spacing-lg);
            color: var(--color-text-medium);
            font-size: 0.875rem;
        }
        
        .login-footer a {
            color: var(--color-primary);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--border-radius-sm);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }
        
        .alert-danger {
            background-color: rgba(160, 83, 68, 0.2);
            color: var(--color-error);
            border: 1px solid var(--color-error);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="../img/AURA (2).png" alt="Aura Deco Logo">
        </div>
        
        <h2 class="login-title">Administration</h2>
        
        <?php
        session_start();
        require_once '../config/database.php';
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Basic validation
            if (empty($username) || empty($password)) {
                echo '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
            } else {
                try {
                    // Prepare query to check user credentials
                    $stmt = $db->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();
                    
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verify password
                    if ($user && password_verify($password, $user['password'])) {
                        // Set session variables
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_username'] = $user['username'];
                        
                        // Redirect to dashboard
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        echo '<div class="alert alert-danger">Nom d\'utilisateur ou mot de passe incorrect.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Erreur de connexion: ' . $e->getMessage() . '</div>';
                }
            }
        }
        ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            
            <button type="submit" class="login-btn">Se connecter</button>
            
            <div class="login-footer">
                <p><a href="../index.html">Retour au site</a></p>
                <p>&copy; 2025 Aura Decoration</p>
            </div>
        </form>
    </div>
</body>
</html>