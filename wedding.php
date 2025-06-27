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
    <title>Think Cafe - Pre-Wedding</title>
    <link rel="stylesheet" href="css/wedding.css"> 
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
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">menu</a></li>
                <li><a href="event.php" class="active">Event</a></li>
             <li class="dropdown">
                <a href="#" class="dropbtn">More ▼</a>
                <div class="dropdown-content">
                    <a href="wedding.php" class="active">Wedding</a>
                    <a href="studios.php">Studios</a>
                    <a href="meeting.php">Meeting</a>
                    <a href="Pmeeting.php">Private-meeting</a>
                </div>
              </li>
        </nav>
    </header>
    <main class="animate-up delay-2">
        <img src="img/weddingpackage.jpg"><br>
        <p class="animate-up delay-3">ทีมงาน BLOC EVENT ของพนักงานที่ทุ่มเทมุ่งมั่นที่จะทำให้เกินความคาดหมายทั้งหมดของคุณเมื่อถึงเวลาสร้างวันที่สมบูรณ์แบบของคุณ
            BLOC เป็นสถานที่ที่สมบูรณ์แบบในการจัดงานแต่งงานและกิจกรรมต่างๆ พื้นที่มีการออกแบบที่ไม่ซ้ำใครมากมายในพื้นที่ส่วนตัวอันงดงามรอบๆ พื้นที่ เพิ่มอาร์เรย์การออกแบบที่เป็นเอกลักษณ์ของคอนเทนเนอร์และต้นไม้ที่คุณรับประกันว่าจะทำให้แขกของคุณต้องว้าว
            งานแต่งงานและกิจกรรมของ BLOC เป็นมากกว่าการวางแผนงานแต่งงานและงานอีเวนต์ ทีมของเราจะให้การสนับสนุนอย่างเต็มที่แก่คุณและแขกของคุณ ช่วยเหลือด้วยคำแนะนำในการออกแบบและอาหารและสิ่งอื่นใดที่คุณจะต้องทำให้คุณจัดงานแต่งงานที่ทุกคนจะพูดถึงในอีกหลายปีข้างหน้า
            
            สำรองที่นั่ง : 085-370-6367, 086-555-8789</p>
            <button onclick="window.location.href='reserveE.php?event=Pre-Wedding'" class="animate-up delay-4">จองวัน (Wedding)</button>
    </main>
</body>
</html>