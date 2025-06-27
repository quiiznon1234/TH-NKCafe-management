<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - THINK Cafe</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<header class="animate-up delay-1">
        <div class="logo">Think Cafe</div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">Menu</a></li>
                <li><a href="event.php">Event</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="login.php" class="active">Sign-up</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1 class="animate-up delay-2">Sign-up</h1>
        <form action="register.php" method="post" class="animate-up delay-3">
            <div class="form-group delay-4">
                <label for="firstName">Name</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group delay-5">
                <label for="lastName">Surname</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group delay-6">
                <label for="phoneNumber">Phone-number</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>
            </div>
            <div class="form-group delay-7">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group delay-8">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group delay-8">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="animate-up delay-8">Submit</button>
        </form>
    </main>
</body>
</html>

<?php
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
    // ใช้ prepared statements เพื่อป้องกัน SQL injection
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName = $conn->real_escape_string(trim($_POST['lastName']));
    $phoneNumber = $conn->real_escape_string(trim($_POST['phoneNumber']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // แฮชรหัสผ่าน
    $email = $conn->real_escape_string(trim($_POST['email']));

    // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูล
    $sql = "INSERT INTO member_cm (firstname, surname, phonenum, username, pass, email) VALUES ('$firstName', '$lastName', '$phoneNumber', '$username', '$password', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ปิดการเชื่อมต่อ
$conn->close();
?>