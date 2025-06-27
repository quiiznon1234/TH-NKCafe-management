<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

require_once 'config.php';

$id = (int)($data['order_id'] ?? 0);
$items = $data['items'] ?? [];

if (!$id || empty($items)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$grouped = [];
$total = 0;

foreach ($items as $item) {
    $name = trim($item['name']);
    $group = trim($item['group'] ?? '');
    $price = floatval($item['price']);

    $key = "$name|$group|$price";
    if (!isset($grouped[$key])) {
        $grouped[$key] = ['name' => $name, 'group' => $group, 'price' => $price, 'qty' => 1];
    } else {
        $grouped[$key]['qty']++;
    }
}

$orderDetails = '';
foreach ($grouped as $g) {
    $qty = $g['qty'];
    $lineTotal = $qty * $g['price'];
    $text = $g['name'];
    if ($g['group']) $text .= " ({$g['group']})";
    $orderDetails .= $text . ($qty > 1 ? " *$qty" : '') . " - " . number_format($lineTotal, 2) . " ฿\n";
    $total += $lineTotal;
}

$conn = new mysqli(); // config as before
if ($conn->connect_error) die(json_encode(['error' => 'DB error']));

$stmt = $conn->prepare("UPDATE orders SET order_details = ?, total = ? WHERE id = ? AND status = 'Pending'");
$stmt->bind_param('sdi', $orderDetails, $total, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'DB update failed']);
}
