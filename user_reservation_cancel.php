<?php
    session_start();
    require_once 'config.php';

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $username = $_SESSION['username'];

        // ตรวจสอบว่าการจองนี้เป็นของผู้ใช้จริงหรือไม่ (ทั้ง table_cm และ event_cm)
        // อัปเดตสถานะเป็น 'ยกเลิก' ใน table_cm
        $sql1 = "UPDATE table_cm SET status = 'ยกเลิก' WHERE id = ? AND username = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("is", $id, $username);
        $stmt1->execute();

        // อัปเดตสถานะเป็น 'ยกเลิก' ใน event_cm
        $sql2 = "UPDATE event_cm SET status = 'ยกเลิก' WHERE id = ? AND username = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("is", $id, $username);
        $stmt2->execute();

        // กลับไปหน้าก่อนหน้า
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // ถ้าไม่มีข้อมูลที่ต้องการ
        header("Location: home.php");
        exit();
    }
?>