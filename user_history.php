<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$username = $_SESSION['username'] ?? null;
if (!$username) {
    echo json_encode(['orders' => [], 'cancelled' => false]);
    exit;
}

$orders = [];
$stmt = $conn->prepare("SELECT id, created_at, order_details, total, status FROM orders WHERE customer_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $lines = explode("\n", trim($row['order_details'] ?? ''));
    $items = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;

        if (preg_match('/^(.+?)\s*\((.+?)\)\s*\*?(\d+)?\s*-\s*([\d.]+)\s*฿$/', $line, $m)) {
            $name = $m[1];
            $qty = $m[3] ?: 1;
            $price = $m[4];
            $items[] = "$name *$qty - $price ฿";
        } else {
            $items[] = $line;
        }
    }

    // ✅ แปลง status 'Completed' → 'Checkout'
    $statusText = match (strtolower($row['status'])) {
        'pending'   => 'Pending',
        'accepted'  => 'Accepted',
        'completed' => 'Checkout',
        default     => ucfirst($row['status'])
    };

    $orders[] = [
        'id' => $row['id'],
        'created_at' => $row['created_at'],
        'details' => implode("\n", $items),
        'total' => $row['total'],
        'status' => $statusText,
    ];
}

// 🔔 ตรวจสอบว่าออเดอร์ล่าสุดเพิ่งถูกยกเลิก
$cancelled = false;
if (isset($_SESSION['last_order_cancelled'])) {
    $cancelled = true;
    unset($_SESSION['last_order_cancelled']);
}

echo json_encode([
    'orders' => $orders,
    'cancelled' => $cancelled
]);
?>