<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$first_name = sanitize($_POST['first_name'] ?? '');
$last_name  = sanitize($_POST['last_name'] ?? '');
$email      = sanitize($_POST['email'] ?? '');
$phone      = sanitize($_POST['phone'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirm_password'] ?? '';

if (!$first_name || !$last_name || !$email || !$password) {
    echo json_encode(['success'=>false,'message'=>'All required fields are missing.']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Invalid email address.']); exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success'=>false,'message'=>'Password must be at least 6 characters.']); exit;
}
if ($password !== $confirm) {
    echo json_encode(['success'=>false,'message'=>'Passwords do not match.']); exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'An account with this email already exists.']); exit;
}
$check->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, is_active) VALUES (?,?,?,?,?,'user',1)");
$stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hash);
if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Account created successfully!','redirect'=>'../login.php']);
} else {
    echo json_encode(['success'=>false,'message'=>'Registration failed. Please try again.']);
}
$stmt->close();
