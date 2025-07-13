<?php
// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Replace with your email
define('SMTP_PASSWORD', 'your-app-password'); // Replace with your app password
define('ADMIN_EMAIL', 'parth@photokrafft.com');

// Email sending function using PHPMailer
function sendEmail($to, $subject, $body, $isHTML = true) {
    require_once 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_USERNAME, 'Photokrafft');
        $mail->addAddress($to);
        $mail->addCC(ADMIN_EMAIL, 'Admin');
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Send confirmation email to customer
function sendCustomerConfirmation($customerEmail, $customerName, $formData) {
    $subject = "Registration Confirmation - Photokrafft Event";
    
    $body = "
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
                <p>Dear <strong>{$customerName}</strong>,</p>
                <p>Thank you for registering with Photokrafft! Your registration has been successfully received.</p>
                
                <h3>Registration Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {$formData['full_name']}</li>
                    <li><strong>Email:</strong> {$formData['email']}</li>
                    <li><strong>Event Name:</strong> {$formData['event_name']}</li>
                    <li><strong>Workshop Name:</strong> {$formData['workshop_name']}</li>
                    <li><strong>Investment Amount:</strong> {$formData['investment']}</li>
                </ul>
                
                <p>We will contact you soon with further details about the event.</p>
                <p>Best regards,<br>Team Photokrafft</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmail($customerEmail, $subject, $body);
}

// Send notification email to admin
function sendAdminNotification($formData) {
    $subject = "New Registration - Photokrafft Event";
    
    $body = "
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
                    <li><strong>Name:</strong> {$formData['full_name']}</li>
                    <li><strong>Email:</strong> {$formData['email']}</li>
                    <li><strong>Event Name:</strong> {$formData['event_name']}</li>
                    <li><strong>Workshop Name:</strong> {$formData['workshop_name']}</li>
                    <li><strong>Investment Amount:</strong> {$formData['investment']}</li>
                    <li><strong>Registration Date:</strong> " . date('Y-m-d H:i:s') . "</li>
                </ul>
            </div>
            <div class='footer'>
                <p>This is an automated notification from the Photokrafft registration system.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmail(ADMIN_EMAIL, $subject, $body);
}
?>