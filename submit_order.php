<?php
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $conn->real_escape_string($data['table'] ?? '');
$customer = isset($_SESSION['username']) ? $_SESSION['username'] : ($data['customer'] ?? 'Guest');
$items = $data['items'] ?? [];

if (empty($items)) {
    echo json_encode(['error' => 'ไม่มีข้อมูลรายการสั่งซื้อ']);
    exit;
}

// ✅ รวมเมนูซ้ำ
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
    }
}

$orderDetails = '';
$total = 0;
foreach ($grouped as $g) {
    $lineTotal = $g['price'] * $g['qty'];
    $groupText = $g['group'] ? " ({$g['group']})" : '';
    $commentText = !empty($g['comment']) ? " [{$g['comment']}]" : '';
    $orderDetails .= "{$g['name']}{$groupText} *{$g['qty']}{$commentText} - $lineTotal ฿\n";
    $total += $lineTotal;
}


$stmt = $conn->prepare("INSERT INTO orders (customer_name, order_details, total, status, table_id) VALUES (?, ?, ?, 'Pending', ?)");
$stmt->bind_param("ssds", $customer, $orderDetails, $total, $table);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'บันทึกคำสั่งซื้อไม่สำเร็จ']);
}
$stmt->close();
$conn->close();
?>