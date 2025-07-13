<?php
/**
 * Photokrafft Form System Setup Script
 * Interactive setup for initial configuration
 */

echo "=== Photokrafft Form System Setup ===\n\n";

// Check if already configured
if (file_exists('config/database.php') && file_exists('config/email.php')) {
    echo "⚠️  Configuration files already exist.\n";
    echo "Do you want to reconfigure? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 'y') {
        echo "Setup cancelled.\n";
        exit;
    }
}

echo "Let's configure your Photokrafft Form System...\n\n";

// Database Configuration
echo "=== Database Configuration ===\n";
echo "Enter your MySQL database details:\n\n";

echo "Database Host (default: localhost): ";
$handle = fopen("php://stdin", "r");
$db_host = trim(fgets($handle)) ?: 'localhost';
fclose($handle);

echo "Database Username: ";
$handle = fopen("php://stdin", "r");
$db_user = trim(fgets($handle));
fclose($handle);

echo "Database Password: ";
$handle = fopen("php://stdin", "r");
$db_pass = trim(fgets($handle));
fclose($handle);

echo "Database Name (default: photokrafft_forms): ";
$handle = fopen("php://stdin", "r");
$db_name = trim(fgets($handle)) ?: 'photokrafft_forms';
fclose($handle);

// Test database connection
echo "\nTesting database connection...\n";
try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!\n";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "✅ Database '$db_name' created/verified\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your credentials and try again.\n";
    exit(1);
}

// Email Configuration
echo "\n=== Email Configuration ===\n";
echo "Enter your SMTP email details:\n\n";

echo "SMTP Host (default: smtp.gmail.com): ";
$handle = fopen("php://stdin", "r");
$smtp_host = trim(fgets($handle)) ?: 'smtp.gmail.com';
fclose($handle);

echo "SMTP Port (default: 587): ";
$handle = fopen("php://stdin", "r");
$smtp_port = trim(fgets($handle)) ?: '587';
fclose($handle);

echo "SMTP Username (your email): ";
$handle = fopen("php://stdin", "r");
$smtp_username = trim(fgets($handle));
fclose($handle);

echo "SMTP Password (app password for Gmail): ";
$handle = fopen("php://stdin", "r");
$smtp_password = trim(fgets($handle));
fclose($handle);

echo "Admin Email (default: parth@photokrafft.com): ";
$handle = fopen("php://stdin", "r");
$admin_email = trim(fgets($handle)) ?: 'parth@photokrafft.com';
fclose($handle);

// Create config directory if not exists
if (!is_dir('config')) {
    mkdir('config', 0755, true);
}

// Generate database config
$db_config = "<?php
// Database configuration
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

// Create database connection
function getDBConnection() {
    try {
        \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME, DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    } catch(PDOException \$e) {
        die(\"Connection failed: \" . \$e->getMessage());
    }
}

// Create database and table if not exists
function initializeDatabase() {
    try {
        // Create database connection without database name
        \$pdo = new PDO(\"mysql:host=\" . DB_HOST, DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        \$pdo->exec(\"CREATE DATABASE IF NOT EXISTS \" . DB_NAME);
        \$pdo->exec(\"USE \" . DB_NAME);
        
        // Create table if not exists
        \$sql = \"CREATE TABLE IF NOT EXISTS form_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            event_name VARCHAR(255),
            workshop_name VARCHAR(255),
            investment VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )\";
        
        \$pdo->exec(\$sql);
        return true;
    } catch(PDOException \$e) {
        die(\"Database initialization failed: \" . \$e->getMessage());
    }
}
?>";

// Generate email config
$email_config = "<?php
// Email configuration
define('SMTP_HOST', '$smtp_host');
define('SMTP_PORT', $smtp_port);
define('SMTP_USERNAME', '$smtp_username');
define('SMTP_PASSWORD', '$smtp_password');
define('ADMIN_EMAIL', '$admin_email');

// Email sending function using PHPMailer
function sendEmail(\$to, \$subject, \$body, \$isHTML = true) {
    require_once 'vendor/autoload.php';
    
    \$mail = new PHPMailer\\PHPMailer\\PHPMailer(true);
    
    try {
        // Server settings
        \$mail->isSMTP();
        \$mail->Host = SMTP_HOST;
        \$mail->SMTPAuth = true;
        \$mail->Username = SMTP_USERNAME;
        \$mail->Password = SMTP_PASSWORD;
        \$mail->SMTPSecure = PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_STARTTLS;
        \$mail->Port = SMTP_PORT;
        
        // Recipients
        \$mail->setFrom(SMTP_USERNAME, 'Photokrafft');
        \$mail->addAddress(\$to);
        \$mail->addCC(ADMIN_EMAIL, 'Admin');
        
        // Content
        \$mail->isHTML(\$isHTML);
        \$mail->Subject = \$subject;
        \$mail->Body = \$body;
        
        \$mail->send();
        return true;
    } catch (Exception \$e) {
        error_log(\"Email sending failed: \" . \$mail->ErrorInfo);
        return false;
    }
}

// Send confirmation email to customer
function sendCustomerConfirmation(\$customerEmail, \$customerName, \$formData) {
    \$subject = \"Registration Confirmation - Photokrafft Event\";
    
    \$body = \"
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #5bb5a2; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { text-align: center; padding: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Registration Confirmed!</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>{\$customerName}</strong>,</p>
                <p>Thank you for registering with Photokrafft! Your registration has been successfully received.</p>
                
                <h3>Registration Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {\$formData['full_name']}</li>
                    <li><strong>Email:</strong> {\$formData['email']}</li>
                    <li><strong>Event Name:</strong> {\$formData['event_name']}</li>
                    <li><strong>Workshop Name:</strong> {\$formData['workshop_name']}</li>
                    <li><strong>Investment Amount:</strong> {\$formData['investment']}</li>
                </ul>
                
                <p>We will contact you soon with further details about the event.</p>
                <p>Best regards,<br>Team Photokrafft</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>\";
    
    return sendEmail(\$customerEmail, \$subject, \$body);
}

// Send notification email to admin
function sendAdminNotification(\$formData) {
    \$subject = \"New Registration - Photokrafft Event\";
    
    \$body = \"
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #333; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { text-align: center; padding: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Registration Received</h1>
            </div>
            <div class='content'>
                <h3>Registration Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {\$formData['full_name']}</li>
                    <li><strong>Email:</strong> {\$formData['email']}</li>
                    <li><strong>Event Name:</strong> {\$formData['event_name']}</li>
                    <li><strong>Workshop Name:</strong> {\$formData['workshop_name']}</li>
                    <li><strong>Investment Amount:</strong> {\$formData['investment']}</li>
                    <li><strong>Registration Date:</strong> \" . date('Y-m-d H:i:s') . \"</li>
                </ul>
            </div>
            <div class='footer'>
                <p>This is an automated notification from the Photokrafft registration system.</p>
            </div>
        </div>
    </body>
    </html>\";
    
    return sendEmail(ADMIN_EMAIL, \$subject, \$body);
}
?>";

// Write configuration files
file_put_contents('config/database.php', $db_config);
file_put_contents('config/email.php', $email_config);

echo "\n✅ Configuration files created successfully!\n";

// Initialize database
echo "\nInitializing database...\n";
require_once 'config/database.php';
initializeDatabase();
echo "✅ Database initialized successfully!\n";

echo "\n=== Setup Complete ===\n";
echo "✅ Database configured\n";
echo "✅ Email configured\n";
echo "✅ All files created\n\n";

echo "Next steps:\n";
echo "1. Run: composer install\n";
echo "2. Test the system: php test.php\n";
echo "3. Access the form: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
echo "4. Access admin panel: " . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/admin/\n";
echo "   Username: admin\n";
echo "   Password: photokrafft2024\n\n";

echo "Setup completed successfully! 🎉\n";
?>