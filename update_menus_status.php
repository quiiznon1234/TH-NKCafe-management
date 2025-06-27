<?php
    require_once 'config.php';
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $closed = isset($data['closed']) ? intval($data['closed']) : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE menu_cm SET is_closed=? WHERE id=?");
        $stmt->bind_param("ii", $closed, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
?>