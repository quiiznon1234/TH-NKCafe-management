<?php
session_start(); // เริ่ม session

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user'; // ค่าเริ่มต้นเป็น user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Reserve table</title>
    <link rel="stylesheet" href="css/reserve.css">
</head>
<body>
    <header class="animate-up delay-1">
        <div class="logo">Think Cafe</div>
        <nav>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><span><?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="logout.php">Log-out</a></li>
                <?php else: ?>
                    <li><a href="register.php">Sign-up</a></li>
                    <li><a href="login.php">Log-in</a></li>
                <?php endif; ?>
                    <li><a href="checkTables.php">Reservations</a></li>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="reserveT.php" class="active">Reserve</a></li>
                    <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">menu</a></li>
                    <li><a href="event.php">Event</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <form class="reserve-form animate-up delay-2" id="reserveForm" method="POST">
            <label for="name" class="animate-up delay-2">Username</label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($isLoggedIn ? $username : ''); ?>" 
            <?php if ($isLoggedIn) echo 'readonly'; ?>>

            <label for="phonenum" class="animate-up delay-3">เบอร์โทรศัพท์</label>
            <input type="text" id="phonenum" name="phonenum" required>

            <label for="seats" class="animate-up delay-4">จำนวนลูกค้าที่มา</label>
            <select id="seats" name="seats">
                <option value="4">4 Seats</option>
                <option value="4-8">4-6 Seats</option>
                <option value="8+">6-8 Seats</option>
            </select>

            <label for="date" class="animate-up delay-5">Date</label>
            <input type="date" id="date" name="date" required>

            <label for="time" class="animate-up delay-6">Time</label>
            <select id="time" name="time" required>
                <option value="6:00 AM - 11:00 AM">6:00 AM - 11:00 AM</option>
                <option value="12:00 AM -15:00 PM">12:00 AM -15:00 PM</option>
                <option value="16:00 PM -18:00 PM">16:00 PM -18:00 PM</option>
                <option value="19:00 PM -21:00 PM">19:00 PM -21:00 PM</option>
            </select>

            <button type="submit" class="animate-up delay-7">จอง</button>
        </form>
    </main>
<?php
            $loginAlert = '';
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !$isLoggedIn) {
                $loginAlert = '<div class="alert" style="color: black; text-align: center; margin: 30px 0;">กรุณาลงชื่อเข้าใช้ก่อนทำการจอง</div>';
            }
            echo $loginAlert;
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && $isLoggedIn) {
// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // รับข้อมูลจากฟอร์ม
    $name = $_POST['username'];
    $phonenum = $_POST['phonenum'];
    $seats = $_POST['seats'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare("INSERT INTO table_cm (username, phonenum, seats, date, time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $phonenum, $seats, $date, $time);

    // บันทึกข้อมูล
    if ($stmt->execute()) {
        header("Location: checkTables.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
