<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'tattoo';
$username = 'root';
$password = '';

try {
    // First try to connect without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Now connect to the database
    $pdo->exec("USE `$dbname`");
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('client', 'artist', 'admin') NOT NULL DEFAULT 'client',
        specialization VARCHAR(100),
        profile_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_email (email)
    )");
    
    // Ensure admin user exists
    $adminUsername = 'decepticon';
    $adminPassword = 'qazwsxedc';
    $adminHashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    $adminEmail = 'admin@example.com'; // Use a placeholder or specific admin email

    // Check if admin exists by username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$adminUsername]);
    if (!$stmt->fetch()) {
        // Admin doesn't exist, create them
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        // Use the defined admin email
        $stmt->execute([$adminUsername, $adminEmail, $adminHashedPassword]); 
        // Optional: Log or echo creation
        // error_log("Admin user 'decepticon' created."); 
    }

    // Test the connection
    $stmt = $pdo->query('SELECT 1');
    if (!$stmt) {
        throw new PDOException('Failed to execute test query');
    }
    
    echo "<!-- Database connected successfully -->";
} catch(PDOException $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; margin: 10px; border: 1px solid #faa;'>
        Database Error: " . $e->getMessage() . "
    </div>";
    die();
}
?>  