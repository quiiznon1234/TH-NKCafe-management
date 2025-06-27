
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    require_once 'config.php';
    $id = (int)$_POST['order_id'];
    $action = $_POST['action'] ?? 'accept'; // default เป็น accept

    if ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param('i', $id); // แก้จาก $order_id เป็น $id
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $ok]);
        exit;
    }
    
    if ($action === 'checkout') {
        // อัพเดท status เป็น Completed
        $stmt = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        // อัพเดท status เป็น Accepted (โค้ดเดิม)
        $stmt = $conn->prepare("UPDATE orders SET status = 'Accepted' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
    }
    
    // รองรับทั้ง AJAX และการโหลดหน้าปกติ
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // กรณี AJAX request ส่งค่ากลับเป็น JSON
        header('Content-Type: application/json');
        if ($success) {
            $message = $action === 'checkout' ? 'Order completed successfully' : 'Order accepted successfully';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database update failed']);
        }
    } else {
        // กรณีไม่ใช่ AJAX ให้ redirect กลับหน้าเดิม
        header("Location: Admin_order.php");
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
exit();
?>