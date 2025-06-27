<?php
session_start(); // เริ่ม session

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Reserve event</title>
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

        <form class="reserve-form animate-up delay-2" id="EventForm" method="POST">
            <label for="event">กิจกรรมที่จะจอง หรือจัด</label>
            <select id="event" name="event">
                <option value="Wedding space">Wedding space</option>
                <option value="Shooting photo Studios no.1">Shooting photo Studios no.1</option>
                <option value="Shooting photo Studios no.2">Shooting photo Studios no.2</option>
                <option value="Shooting photo Studios no.3">Shooting photo Studios no.3</option>
                <option value="Meeting room">Meeting room</option>
                <option value="Private-Meeting">Private-Meeting</option>
            </select>

            <label for="name" class="animate-up delay-3">Username</label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($isLoggedIn ? $username : ''); ?>" 
            <?php if ($isLoggedIn) echo 'readonly'; ?>>

            <label for="phonenum" class="animate-up delay-4">เบอร์โทรศัพท์</label>
            <input type="text" id="phonenum" name="phonenum" required>

            <label for="guests" class="animate-up delay-5">จำนวนผู้เข้ากิจกรรม</label>
            <input type="number" id="guests" name="guests" required>

            <label for="date" class="animate-up delay-6">Date</label>
            <input type="date" id="date" name="date" required>

            <label for="time" class="animate-up delay-7">Time</label>
            <select id="time" name="time" required>
                <option value="6:00 AM - 12:00 AM">6:00 AM - 12:00 AM</option>
                <option value="13:00 AM -18:00 PM">13:00 AM -18:00 PM</option>
                <option value="ทั้งวัน">ทั้งวัน</option>
            </select>

            <button type="submit" class="animate-up delay-8">จอง</button>
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
        $event = $_POST['event'];
        $name = $_POST['username'];
        $phonenum = $_POST['phonenum'];
        $guests = $_POST['guests'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("INSERT INTO event_cm ( event, username, phonenum, guests, date, time) VALUES ( ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $event, $username, $phonenum, $guests, $date, $time);

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