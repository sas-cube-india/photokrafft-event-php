<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$pdo = getDBConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'view':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM form_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($submission) {
            echo json_encode(['success' => true, 'submission' => $submission]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Submission not found']);
        }
        break;
        
    case 'update':
        $id = $_POST['id'] ?? 0;
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $eventName = trim($_POST['event_name'] ?? '');
        $workshopName = trim($_POST['workshop_name'] ?? '');
        $investment = trim($_POST['investment'] ?? '');
        
        if (empty($fullName) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Full name and email are required']);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }
        
        $sql = "UPDATE form_submissions SET 
                full_name = ?, 
                email = ?, 
                phone = ?,
                event_name = ?, 
                workshop_name = ?, 
                investment = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$fullName, $email, $phone, $eventName, $workshopName, $investment, $id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Submission updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating submission']);
        }
        break;
        
    case 'delete':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM form_submissions WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Submission deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting submission']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
