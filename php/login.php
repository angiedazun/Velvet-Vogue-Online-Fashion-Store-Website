<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? 'login';

if ($action === 'login') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) { echo json_encode(['success'=>false,'message'=>'Email and password required.']); exit; }

    $stmt = $conn->prepare("SELECT id, first_name, last_name, password, role, is_active FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        if (!$user['is_active']) {
            echo json_encode(['success'=>false,'message'=>'Your account is suspended.']); exit;
        }
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            $redirect = $user['role'] === 'admin' ? '../admin/index.php' : '../index.php';
            echo json_encode(['success'=>true,'message'=>'Login successful!','redirect'=>$redirect,'role'=>$user['role']]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Incorrect password.']);
        }
    } else {
        echo json_encode(['success'=>false,'message'=>'No account found with that email.']);
    }
    $stmt->close();
}
