<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$q = sanitize($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }

$q = '%' . $conn->real_escape_string($q) . '%';
$res = $conn->query("SELECT id, name, slug, price, sale_price, images FROM products WHERE (name LIKE '$q' OR description LIKE '$q') LIMIT 8");

$results = [];
while ($row = $res->fetch_assoc()) {
    $price = ($row['sale_price'] && $row['sale_price'] > 0) ? $row['sale_price'] : $row['price'];
    $results[] = [
        'id'    => $row['id'],
        'name'  => htmlspecialchars($row['name']),
        'slug'  => htmlspecialchars($row['slug']),
        'price' => 'Rs. ' . number_format($price),
        'image' => htmlspecialchars($row['images']),
        'url'   => 'product-detail.php?id=' . $row['id']
    ];
}

echo json_encode($results);
