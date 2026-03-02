<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $pid  = (int)($_POST['product_id'] ?? 0);
        $qty  = max(1, (int)($_POST['quantity'] ?? 1));
        $size = sanitize($_POST['size'] ?? '');
        $color= sanitize($_POST['color'] ?? '');

        // Check product exists & in stock
        $pRes = $conn->query("SELECT id, stock FROM products WHERE id=$pid");
        if (!$pRes || $pRes->num_rows == 0) { echo json_encode(['success'=>false,'message'=>'Product not found.']); exit; }
        $prod = $pRes->fetch_assoc();
        if ($prod['stock'] < $qty) { echo json_encode(['success'=>false,'message'=>'Not enough stock available.']); exit; }

        if (isLoggedIn()) {
            $uid = $_SESSION['user_id'];
            // Check if already in cart
            $ex = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=? AND size=? AND color=?");
            $ex->bind_param("iiss", $uid, $pid, $size, $color);
            $ex->execute();
            $exRes = $ex->get_result();
            if ($row = $exRes->fetch_assoc()) {
                $newQty = $row['quantity'] + $qty;
                $conn->query("UPDATE cart SET quantity=$newQty WHERE id={$row['id']}");
            } else {
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, size, color) VALUES (?,?,?,?,?)");
                $stmt->bind_param("iiiss", $uid, $pid, $qty, $size, $color);
                $stmt->execute();
                $stmt->close();
            }
            $ex->close();
            $count = getCartCount($conn, $uid);
        } else {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            $key = $pid . '_' . $size . '_' . $color;
            if (isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]['quantity'] += $qty;
            } else {
                $_SESSION['cart'][$key] = ['product_id'=>$pid,'quantity'=>$qty,'size'=>$size,'color'=>$color];
            }
            $count = array_sum(array_column($_SESSION['cart'], 'quantity'));
        }
        echo json_encode(['success'=>true,'message'=>'Added to cart!','cart_count'=>$count]);
        break;

    case 'remove':
        $itemId = (int)($_POST['item_id'] ?? 0);
        if (isLoggedIn()) {
            $uid = $_SESSION['user_id'];
            $conn->query("DELETE FROM cart WHERE id=$itemId AND user_id=$uid");
            $count = getCartCount($conn, $uid);
            $sub = getCartSubtotal($conn, $uid);
            $ship = $sub >= 3000 ? 0 : 200;
            echo json_encode(['success'=>true,'cart_count'=>$count,'subtotal'=>'Rs. '.number_format($sub),'total'=>'Rs. '.number_format($sub+$ship)]);
        } else {
            // Session cart
            // itemId acts as array key index here
            echo json_encode(['success'=>true,'cart_count'=>0]);
        }
        break;

    case 'update':
        $itemId = (int)($_POST['item_id'] ?? 0);
        $qty    = max(1, (int)($_POST['quantity'] ?? 1));
        if (isLoggedIn()) {
            $uid = $_SESSION['user_id'];
            $conn->query("UPDATE cart SET quantity=$qty WHERE id=$itemId AND user_id=$uid");
            // Recalculate item total
            $res = $conn->query("SELECT c.quantity, IFNULL(NULLIF(p.sale_price,0),p.price) as price FROM cart c JOIN products p ON c.product_id=p.id WHERE c.id=$itemId");
            $item = $res ? $res->fetch_assoc() : null;
            $itemTotal = $item ? $item['price'] * $item['quantity'] : 0;
            $sub  = getCartSubtotal($conn, $uid);
            $ship = $sub >= 3000 ? 0 : 200;
            $count = getCartCount($conn, $uid);
            echo json_encode(['success'=>true,'item_total'=>'Rs. '.number_format($itemTotal),'subtotal'=>'Rs. '.number_format($sub),'total'=>'Rs. '.number_format($sub+$ship),'cart_count'=>$count]);
        } else {
            echo json_encode(['success'=>true]);
        }
        break;

    case 'clear':
        if (isLoggedIn()) {
            $uid = $_SESSION['user_id'];
            $conn->query("DELETE FROM cart WHERE user_id=$uid");
        } else {
            $_SESSION['cart'] = [];
        }
        echo json_encode(['success'=>true,'cart_count'=>0]);
        break;

    case 'get':
        $uid = isLoggedIn() ? (int)$_SESSION['user_id'] : 0;
        $count = $uid ? getCartCount($conn, $uid) : (isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'],'quantity')) : 0);
        echo json_encode(['success'=>true,'count'=>$count]);
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Invalid action.']);
}

function getCartSubtotal($conn, $uid) {
    $res = $conn->query("SELECT SUM(c.quantity * IFNULL(NULLIF(p.sale_price,0),p.price)) as sub FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$uid");
    $row = $res->fetch_assoc();
    return (float)($row['sub'] ?? 0);
}
