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
    <title>Think Cafe - Private Meeting</title>
    <link rel="stylesheet" href="css/Pmeeting.css">
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
                    <a href="wedding.php">Wedding</a>
                    <a href="studios.php">Studios</a>
                    <a href="meeting.php">Meeting</a>
                    <a href="Pmeeting.php" class="active">Private-meeting</a>
                </div>
              </li>
        </nav>
    </header>

    <main class="animate-up delay-2">
        <h1 class="animate-up delay-2">Private Meeting</h1>
        
        <img src="img/private.jfif" alt="Private" class="private-image animate-up delay-3">
        
        <div class="room-info animate-up delay-3">
            <div class="room-title">CONTAINER ROOM SIZE XL</div>
            <div class="room-capacity">Max capacity 50</div>
            <div class="room-hours">Available booking hour 9am - 8pm</div>
        </div>

        <div class="pricing-section animate-up delay-4">
            <div class="pricing-title">prices vat.included</div>
            
            <div class="pricing-table">
                <div class="pricing-header">hourly</div>
                <div class="pricing-header">half-day *4hrs</div>
                <div class="pricing-header">full-day *8hrs</div>
            </div>
            
            <div class="pricing-table">
                <div class="pricing-price">1500 ฿</div>
                <div class="pricing-price">5500 ฿</div>
                <div class="pricing-price">9500 ฿</div>
            </div>
        </div>

        <div class="features-section animate-up delay-5">
            <ul class="features-list">
                <li>Free Highspeed WIFI</li>
                <li>TV screen on request
                    <div class="feature-detail">we provide you with 32" TV screen<br>or projector</div>
                </li>
                <li>Microphones
                    <div class="feature-detail">we have a full sound system including 2 microphones</div>
                </li>
                <li>Whiteboard on request</li>
                <li>Free water for guest in your event</li>
                <li>Food & Beverage
                    <div class="feature-detail">Enjoy your coffee break and/or<br>lunch on request</div>
                </li>
            </ul>
            
            <div class="special-note">
                *NOT ALLOWED<br>
                FOOD AND DRINK FROM OUTSIDE!*
            </div>
        </div>

        <button class="reserve-btn animate-up delay-6" onclick="window.location.href='reserveE.php?event=Ballroom'">Reserve</button>
    </main>
</body>
</html>