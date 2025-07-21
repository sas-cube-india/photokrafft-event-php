<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';
require_once 'config/email.php';

// Initialize database
initializeDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $eventName = trim($_POST['eventName'] ?? '');
        $workshopName = trim($_POST['workshopName'] ?? '');
        $investment = trim($_POST['investment'] ?? '');
        
        // Validate required fields
        if (empty($fullName) || empty($email)) {
            throw new Exception('Full name and email are required');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // Prepare form data
        $formData = [
            'full_name' => $fullName,
            'email' => $email,
            'event_name' => $eventName,
            'workshop_name' => $workshopName,
            'investment' => $investment
        ];
        
        // Save to database
        $pdo = getDBConnection();
        $sql = "INSERT INTO form_submissions (full_name, email, event_name, workshop_name, investment) 
                VALUES (:full_name, :email, :event_name, :workshop_name, :investment)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($formData);
        
        // Send confirmation email to customer
        $customerEmailSent = sendCustomerConfirmation($email, $fullName, $formData);
        
        // Send notification email to admin
        $adminEmailSent = sendAdminNotification($formData);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Thank you for registering with Photokrafft.',
            'customer_email_sent' => $customerEmailSent,
            'admin_email_sent' => $adminEmailSent
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?>