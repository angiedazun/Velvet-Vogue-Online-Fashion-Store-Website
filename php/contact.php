<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$name    = sanitize($_POST['name'] ?? '');
$email   = sanitize($_POST['email'] ?? '');
$phone   = sanitize($_POST['phone'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(['success'=>false,'message'=>'Name, email and message are required.']); exit;
}

$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Your message has been sent! We\'ll respond within 24 hours.']);
} else {
    echo json_encode(['success'=>false,'message'=>'Failed to send message. Please try again.']);
}
$stmt->close();
