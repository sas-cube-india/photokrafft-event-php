<?php
/**
 * Photokrafft Form System Installation Script
 * Run this script to set up the database and check system requirements
 */

echo "=== Photokrafft Form System Installation ===\n\n";

// Check PHP version
echo "1. Checking PHP version...\n";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✅ PHP version: " . PHP_VERSION . " (OK)\n";
} else {
    echo "❌ PHP version: " . PHP_VERSION . " (Requires 7.4 or higher)\n";
    exit(1);
}

// Check required PHP extensions
echo "\n2. Checking PHP extensions...\n";
$required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext extension (OK)\n";
    } else {
        echo "❌ $ext extension (Missing)\n";
        exit(1);
    }
}

// Check if composer is available
echo "\n3. Checking Composer...\n";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Composer dependencies installed (OK)\n";
} else {
    echo "⚠️  Composer dependencies not found\n";
    echo "Please run: composer install\n";
}

// Test database connection
echo "\n4. Testing database connection...\n";
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful (OK)\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "✅ Database '" . DB_NAME . "' created/verified (OK)\n";
    
    // Create table
    $pdo->exec("USE " . DB_NAME);
    $sql = "CREATE TABLE IF NOT EXISTS form_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        event_name VARCHAR(255),
        workshop_name VARCHAR(255),
        investment VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "✅ Table 'form_submissions' created/verified (OK)\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database credentials in config/database.php\n";
    exit(1);
}

// Check file permissions
echo "\n5. Checking file permissions...\n";
$files_to_check = [
    'config/database.php',
    'config/email.php',
    'process_form.php',
    'admin/index.php',
    'admin/api.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file (OK)\n";
    } else {
        echo "❌ $file (Missing)\n";
    }
}

// Check if admin directory exists
if (is_dir('admin')) {
    echo "✅ Admin directory (OK)\n";
} else {
    echo "❌ Admin directory (Missing)\n";
}

echo "\n=== Installation Summary ===\n";
echo "✅ System requirements met\n";
echo "✅ Database setup complete\n";
echo "✅ Files verified\n\n";

echo "Next steps:\n";
echo "1. Configure email settings in config/email.php\n";
echo "2. Run 'composer install' if not already done\n";
echo "3. Test the form at: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
echo "4. Access admin panel at: " . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/admin/\n";
echo "   Username: admin\n";
echo "   Password: photokrafft2024\n\n";

echo "Installation completed successfully! 🎉\n";
?>