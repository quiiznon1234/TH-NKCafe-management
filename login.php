<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Log-in</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<header class="animate-up delay-1">
    <div class="logo">Think Cafe</div>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="register.php">Sign-up</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1 class="animate-up delay-2">Log-in</h1>
    <form action="login.php" method="post" class="animate-up delay-3">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="animate-up delay-4">Login</button>
    </form>
</main>
</body>
</html>

<?php
session_start(); // เริ่ม session

// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

$conn = new mysqli(
    $DB_CONFIG['host'],
    $DB_CONFIG['user'],
    $DB_CONFIG['pass'],
    $DB_CONFIG['name'],
    $DB_CONFIG['port']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);

    if ($username === 'admin') {
        // ตรวจสอบจากตาราง admin_cm
        $sql = "SELECT * FROM admin_cm WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // ตรวจสอบรหัสผ่านว่าเป็น "@dmin" (คุณอาจใช้ password_verify ถ้ารหัสผ่านถูกเข้ารหัสไว้)
            if ($password === '@dmin') {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin';
                header("Location: home.php");
                exit();
            } else {
                echo "รหัสผ่านสำหรับผู้ดูแลไม่ถูกต้อง";
            }
        } else {
            echo "ไม่พบผู้ดูแลระบบนี้";
        }

        $stmt->close();
    } else {
        // ตรวจสอบจากตารางสมาชิก
        $sql = "SELECT * FROM member_cm WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['pass'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header("Location: home.php");
                exit();
            } else {
                echo "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            echo "ไม่พบชื่อผู้ใช้นี้";
        }

        $stmt->close();
    }
}

$conn->close();
?>