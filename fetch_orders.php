<?php
    require_once 'config.php';
header('Content-Type: application/json');
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$rows = [];
while ($row = $result->fetch_assoc()) {
    // สร้าง order_details_html แบบเดียวกับใน Admin_order.php
    $lines = explode("\n", trim($row['order_details']));
    $grouped = [];

    foreach ($lines as $line) {
        // Regex ที่สามารถแยก comment [ ] ออกมาได้
        if (preg_match('/^(.*?)(?:\s*\*(\d+))?(?:\s*\[(.*?)\])?\s*-\s*(\d+(?:\.\d+)?)\s*฿$/u', trim($line), $matches)) {
            $name = trim($matches[1]);
            $qty = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : 1;
            $comment = $matches[3] ?? '';
            $lineTotal = (float)$matches[4];
            $price = $lineTotal / $qty;

            $key = $name . '|' . $comment;
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['name' => $name, 'price' => $price, 'qty' => 1, 'comment' => $comment];
            } else {
                $grouped[$key]['qty']++;
                if (!empty($comment)) {
                $grouped[$key]['comment'] .= ' | ' . $comment;
                }
            }
        }
    }
    $detailsHtml = '';
    foreach ($grouped as $g) {
        $lineTotal = $g['price'] * $g['qty'];
        $detailsHtml .= htmlspecialchars($g['name']);
        if ($g['qty'] > 1) {
            $detailsHtml .= " *{$g['qty']}";
        }

        if (!empty($g['comment'])) {
        $detailsHtml .= "<br><small style='color:#666;'>&nbsp;&nbsp;📝 " . htmlspecialchars($g['comment']) . "</small>";
        }
        $detailsHtml .= " - " . number_format($lineTotal, 2) . " ฿<br>";
    }
    $rows[] = [
        'id' => $row['id'],
        'customer_name' => htmlspecialchars($row['customer_name']),
        'table_id' => $row['table_id'] ?? '-',
        'date' => date('d M Y', strtotime($row['created_at'])),
        'time' => date('H:i A', strtotime($row['created_at'])),
        'order_details_html' => $detailsHtml,
        'total' => $row['total'],
        'status' => $row['status']
    ];
}
echo json_encode($rows);
$conn->close();
?>