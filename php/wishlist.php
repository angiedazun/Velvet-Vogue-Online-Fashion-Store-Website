<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success'=>false,'status'=>'login','message'=>'Please login to add to wishlist.']);
    exit;
}

$uid    = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Clear all wishlist items
if ($action === 'clear_all') {
    $conn->query("DELETE FROM wishlist WHERE user_id=$uid");
    echo json_encode(['success'=>true,'message'=>'Wishlist cleared.']);
    exit;
}

$pid = (int)($_POST['product_id'] ?? 0);

if (!$pid) { echo json_encode(['success'=>false,'message'=>'Invalid product.']); exit; }

$check = $conn->query("SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
if ($check && $check->num_rows > 0) {
    $conn->query("DELETE FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    echo json_encode(['success'=>true,'status'=>'removed','message'=>'Removed from wishlist.']);
} else {
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)");
    $stmt->bind_param("ii", $uid, $pid);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>true,'status'=>'added','message'=>'Added to wishlist!']);
}
