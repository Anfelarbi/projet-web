<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        header('Location: Contact.html?status=error&message=all_fields_required');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: Contact.html?status=error&message=invalid_email');
        exit;
    }
    
    try {
        // Save message to database
        $stmt = $db->prepare("INSERT INTO messages (nom, email, message, date_envoi) VALUES (:nom, :email, :message, NOW())");
        $stmt->bindParam(':nom', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        // Send email notification (if desired)
        $to = "contact@aura-deco.com"; // Replace with your email
        $subject = "Nouveau message de $name";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $email_message = "Nom: $name\n";
        $email_message .= "Email: $email\n\n";
        $email_message .= "Message:\n$message";
        
        // Uncomment to send actual email
        // mail($to, $subject, $email_message, $headers);
        
        header('Location: Contact.html?status=success');
    } catch (PDOException $e) {
        // Log the error (in a real application)
        error_log('Contact form error: ' . $e->getMessage());
        
        header('Location: Contact.html?status=error&message=database_error');
    }
} else {
    // If not a POST request, redirect to contact page
    header('Location: Contact.html');
}