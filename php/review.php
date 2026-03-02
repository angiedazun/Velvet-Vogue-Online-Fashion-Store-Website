<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isLoggedIn()) { echo json_encode(['success'=>false,'message'=>'Please login to submit a review.']); exit; }

$pid     = (int)($_POST['product_id'] ?? 0);
$rating  = min(5, max(1, (int)($_POST['rating'] ?? 5)));
$comment = sanitize($_POST['comment'] ?? '');
$uid     = (int)$_SESSION['user_id'];
$name    = sanitize($_SESSION['user_name'] ?? 'Anonymous');

if (!$pid || !$comment) { echo json_encode(['success'=>false,'message'=>'Product ID and comment are required.']); exit; }

// Check for duplicate review
$check = $conn->query("SELECT id FROM reviews WHERE product_id=$pid AND user_id=$uid");
if ($check && $check->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'You have already reviewed this product.']); exit;
}

$stmt2 = $conn->prepare("INSERT INTO reviews (product_id, user_id, reviewer_name, rating, comment) VALUES (?,?,?,?,?)");
$stmt2->bind_param("iisis", $pid, $uid, $name, $rating, $comment);
if ($stmt2->execute()) {
    // Update product average rating
    $conn->query("UPDATE products SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id=$pid) WHERE id=$pid");
    echo json_encode(['success'=>true,'message'=>'Review submitted successfully!','reviewer_name'=>$name,'rating'=>$rating,'comment'=>htmlspecialchars($comment)]);
} else {
    echo json_encode(['success'=>false,'message'=>'Failed to submit review.']);
}
$stmt2->close();
