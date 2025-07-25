<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';
require_once 'config/email.php';

// Initialize database
initializeDatabase();

// 🔁 Step 1: Handle background email mode
if (php_sapi_name() === 'cli' && isset($argv[1])) {
    $formData = json_decode($argv[1], true);
    if (is_array($formData)) {
        sendCustomerConfirmation($formData['email'], $formData['full_name'], $formData);
        sendAdminNotification($formData);
    }
    exit;
}

// 🔁 Step 2: Handle frontend POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
	$phone = trim($_POST['phone'] ?? '');
        $eventName = trim($_POST['eventName'] ?? '');
        $workshopName = trim($_POST['workshopName'] ?? '');
        $investment = trim($_POST['investment'] ?? '');

        if (empty($fullName) || empty($email)) {
            throw new Exception('Full name and email are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        $formData = [
            'full_name' => $fullName,
            'email' => $email,
            'event_name' => $eventName,
            'workshop_name' => $workshopName,
            'investment' => $investment,
	    'phone' => $phone        
            ];

        // Save to database
        $pdo = getDBConnection();
        $sql = "INSERT INTO form_submissions (full_name, email, phone, event_name, workshop_name, investment) 
                VALUES (:full_name, :email, :phone, :event_name, :workshop_name, :investment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($formData);

        // ✅ Respond to frontend
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Thank you for registering with Photokrafft.'
        ]);

        // ✅ Fire new PHP process to send emails
        $json = escapeshellarg(json_encode($formData));
        $path = __FILE__;
        $cmd = "php $path $json > /dev/null 2>&1 &";
        shell_exec($cmd);

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
