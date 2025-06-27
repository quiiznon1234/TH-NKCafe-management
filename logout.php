<?php
session_start(); // เริ่ม session
session_unset(); // ลบข้อมูลทั้งหมดใน session
session_destroy(); // ยกเลิก session

// หลังจากออกจากระบบสำเร็จ ให้ redirect ไปที่หน้า login หรือหน้าแรก
header("Location: login.php");
exit();
