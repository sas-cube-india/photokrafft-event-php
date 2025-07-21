<?php
// Email configuration
define('SMTP_HOST', 'smtp.zoho.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@photokrafftalbums.com');
define('SMTP_PASSWORD', '$');
define('ADMIN_EMAIL', '');

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
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // ✅ For port 587
        $mail->Port = 587;
        $mail->SMTPAutoTLS = true; // ✅ Optional; allows STARTTLS fallback

        // Enable detailed SMTP debug logging
        $mail->SMTPDebug = 2; // 0 = off, 2 = verbose
        $mail->Debugoutput = function($str, $level) {
            file_put_contents(__DIR__ . '/smtp-debug.log', "[" . $level . "] " . $str . "\n", FILE_APPEND);
        };

        // Recipients
        $mail->setFrom(SMTP_USERNAME, 'Photokrafft');
        $mail->addAddress($to);
        $mail->addCC(ADMIN_EMAIL, 'Admin');

        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        // Optional success log
        file_put_contents(__DIR__ . '/smtp-debug.log', "[SUCCESS] Email sent to {$to}\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        // Error log
        $error = $mail->ErrorInfo;
        error_log("Email sending failed: " . $error);
        file_put_contents(__DIR__ . '/smtp-debug.log', "[ERROR] Email sending failed to {$to}: {$error}\n", FILE_APPEND);
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
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #fff; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2e6f40; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { text-align: center; padding: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
  		<img src='https://photokrafft.com/assets/img/logo.png' alt='Photokrafft Logo' style='max-width: 150px; margin: 0 auto 10px; display: block;'>
                <h1>Registration Confirmed!</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>{$customerName}</strong>,</p>
                <p>Thank you for registering with Photokrafft! Your registration has been successfully received.</p>
                <p>We appreciate you visiting our booth at Maternity & Newborn Photographer's Summit 2025.</p>
                <h3>Your Registration Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {$formData['full_name']}</li>
                    <li><strong>Email:</strong> {$formData['email']}</li>
                    <li><strong>Event Name:</strong> {$formData['event_name']}</li>
                    <li><strong>Workshop Name:</strong> {$formData['workshop_name']}</li>
                    <li><strong>Investment Amount:</strong> {$formData['investment']}</li>
                </ul>
                
                <p>Upon confirmation, your coupons will be available for access under your Photokrafft Account.</p>

 		<p>Thank you once again!</p>

		<p>Looking forward to working on your first Lullabook / Bumpbook</p>
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
