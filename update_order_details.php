<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$order_id = (int)($data['order_id'] ?? 0);
$items = $data['items'] ?? [];

require_once 'config.php';

if ($order_id && is_array($items) && count($items) === 0) {
    $delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete->bind_param("i", $order_id);
    if ($delete->execute()) {
        $_SESSION['last_order_cancelled'] = true;
        echo json_encode(['success' => true, 'message' => 'ออเดอร์ถูกยกเลิกและลบเรียบร้อย']);
    } else {
        echo json_encode(['error' => 'ไม่สามารถลบออเดอร์ได้']);
    }
    $delete->close();
    exit;
}

if (!$order_id || !is_array($items)) {
    echo json_encode(['error' => 'ไม่มีข้อมูล']);
    exit;
}

if ($action === 'cancel' && $order_id) {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'ออเดอร์ถูกยกเลิกเรียบร้อย']);
    } else {
        echo json_encode(['error' => 'ไม่สามารถยกเลิกออเดอร์ได้']);
    }
    $stmt->close();
    exit;
}

$grouped = [];
foreach ($items as $item) {
    $name = $conn->real_escape_string($item['name']);
    $group = $conn->real_escape_string($item['group'] ?? '');
    $price = floatval($item['price']);
    $qty = isset($item['qty']) ? intval($item['qty']) : 1;
    $comment = $conn->real_escape_string($item['comment'] ?? '');

    $key = $name . '|' . $comment;

    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'name' => $name,
            'group' => $group,
            'price' => $price,
            'qty' => $qty,
            'comment' => $comment
        ];
    } else {
        $grouped[$key]['qty'] += $qty;
        if (!empty($comment)) {
            $grouped[$key]['comment'] .= ' | ' . $comment;
        }
    }
}

$orderDetails = '';
$total = 0;
foreach ($grouped as $g) {
    $lineTotal = $g['price'] * $g['qty'];
    $groupText = $g['group'] ? " ({$g['group']})" : '';
    $commentText = isset($g['comment']) && $g['comment'] ? " [{$g['comment']}]" : '';
    $orderDetails .= "{$g['name']}{$groupText} *{$g['qty']}{$commentText} - $lineTotal ฿\n";
    $total += $lineTotal;
}


// 🔄 อัปเดตคำสั่งซื้อ
$stmt = $conn->prepare("UPDATE orders SET order_details = ?, total = ? WHERE id = ?");
$stmt->bind_param("sdi", $orderDetails, $total, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'อัปเดตไม่สำเร็จ']);
}

$stmt->close();
$conn->close();
?>