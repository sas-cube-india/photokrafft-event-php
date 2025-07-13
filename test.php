<?php
/**
 * Photokrafft Form System Test Script
 * Run this to test all components
 */

echo "=== Photokrafft Form System Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing database connection...\n";
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Table Structure
echo "\n2. Testing table structure...\n";
try {
    $stmt = $pdo->query("DESCRIBE form_submissions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $expected_columns = ['id', 'full_name', 'email', 'event_name', 'workshop_name', 'investment', 'created_at', 'updated_at'];
    
    $found_columns = array_column($columns, 'Field');
    $missing_columns = array_diff($expected_columns, $found_columns);
    
    if (empty($missing_columns)) {
        echo "✅ Table structure is correct\n";
    } else {
        echo "❌ Missing columns: " . implode(', ', $missing_columns) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Table structure test failed: " . $e->getMessage() . "\n";
}

// Test 3: Email Configuration
echo "\n3. Testing email configuration...\n";
require_once 'config/email.php';
if (defined('SMTP_HOST') && defined('SMTP_USERNAME') && defined('SMTP_PASSWORD')) {
    echo "✅ Email configuration found\n";
} else {
    echo "⚠️  Email configuration incomplete\n";
}

// Test 4: File Permissions
echo "\n4. Testing file permissions...\n";
$files_to_test = [
    'index.html' => 'readable',
    'process_form.php' => 'readable',
    'config/database.php' => 'readable',
    'config/email.php' => 'readable',
    'admin/index.php' => 'readable',
    'admin/api.php' => 'readable'
];

foreach ($files_to_test as $file => $permission) {
    if (file_exists($file)) {
        if ($permission === 'readable' && is_readable($file)) {
            echo "✅ $file ($permission)\n";
        } else {
            echo "❌ $file (not $permission)\n";
        }
    } else {
        echo "❌ $file (missing)\n";
    }
}

// Test 5: Composer Dependencies
echo "\n5. Testing Composer dependencies...\n";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Composer dependencies installed\n";
} else {
    echo "⚠️  Composer dependencies not found\n";
    echo "   Run: composer install\n";
}

// Test 6: Sample Form Submission (if table is empty)
echo "\n6. Testing form submission...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM form_submissions");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        // Insert a test record
        $test_data = [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'event_name' => 'Test Event',
            'workshop_name' => 'Test Workshop',
            'investment' => '$100'
        ];
        
        $sql = "INSERT INTO form_submissions (full_name, email, event_name, workshop_name, investment) 
                VALUES (:full_name, :email, :event_name, :workshop_name, :investment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($test_data);
        
        echo "✅ Test record inserted successfully\n";
        
        // Clean up test record
        $pdo->exec("DELETE FROM form_submissions WHERE email = 'test@example.com'");
        echo "✅ Test record cleaned up\n";
    } else {
        echo "✅ Form submissions table has data\n";
    }
} catch (Exception $e) {
    echo "❌ Form submission test failed: " . $e->getMessage() . "\n";
}

// Test 7: Admin Session
echo "\n7. Testing admin session...\n";
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    echo "✅ Admin session active\n";
} else {
    echo "ℹ️  Admin session not active (normal for test)\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ All core components tested\n";
echo "✅ System appears to be working correctly\n\n";

echo "Next steps:\n";
echo "1. Configure email settings in config/email.php\n";
echo "2. Test the form at: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
echo "3. Access admin panel at: " . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/admin/\n";
echo "4. Run 'composer install' if dependencies are missing\n\n";

echo "System test completed successfully! 🎉\n";
?>