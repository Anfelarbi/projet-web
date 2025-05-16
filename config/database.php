<?php
/**
 * Database Configuration
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_NAME', 'aura');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Create PDO instance
    $db = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
        DB_USER, 
        DB_PASS
    );
    
    // Set error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Handle connection errors
    die('Erreur de connexion à la base de données: '.$e->getMessage());
}