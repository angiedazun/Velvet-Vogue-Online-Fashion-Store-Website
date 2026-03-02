<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

$userId = (int)($_GET['user_id'] ?? 0);
if (!$userId) {
    echo json_encode(['error' => 'Invalid user ID']); exit;
}

$user = $conn->query("SELECT id, first_name, last_name, email, phone, created_at, is_active, role FROM users WHERE id=$userId AND role='user' LIMIT 1")->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => 'User not found']); exit;
}

$orders = $conn->query("SELECT COUNT(*) as total_orders, IFNULL(SUM(total_amount),0) as total_spent, SUM(IF(status='delivered',1,0)) as delivered FROM orders WHERE user_id=$userId")->fetch_assoc();
$lastOrder = $conn->query("SELECT order_number, total_amount, status, created_at FROM orders WHERE user_id=$userId ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
$wishCount = $conn->query("SELECT COUNT(*) as c FROM wishlist WHERE user_id=$userId")->fetch_assoc()['c'] ?? 0;

echo json_encode([
    'id'           => $user['id'],
    'name'         => htmlspecialchars($user['first_name'] . ' ' . $user['last_name']),
    'first_name'   => htmlspecialchars($user['first_name']),
    'last_name'    => htmlspecialchars($user['last_name']),
    'email'        => htmlspecialchars($user['email']),
    'phone'        => htmlspecialchars($user['phone'] ?? '—'),
    'is_active'    => $user['is_active'] ? 'Active' : 'Suspended',
    'created_at'   => date('d M Y', strtotime($user['created_at'])),
    'total_orders' => (int)$orders['total_orders'],
    'total_spent'  => number_format($orders['total_spent'], 2),
    'delivered'    => (int)$orders['delivered'],
    'wish_count'   => (int)$wishCount,
    'last_order'   => $lastOrder ? [
        'number'   => htmlspecialchars($lastOrder['order_number']),
        'amount'   => number_format($lastOrder['total_amount'], 2),
        'status'   => ucfirst($lastOrder['status']),
        'date'     => date('d M Y', strtotime($lastOrder['created_at'])),
    ] : null,
]);
