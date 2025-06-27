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
    <title>Think Cafe - Meeting</title>
    <link rel="stylesheet" href="css/meeting.css">
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
                    <a href="meeting.php" class="active">Meeting</a>
                    <a href="Pmeeting.php">Private-meeting</a>
                </div>
              </li>
        </nav>
    </header>
     <main class="animate-up delay-2">
        <h1 class="animate-up delay-2">Meeting Room</h1>
        
        <div class="rooms-container">
            <!-- Container Room Size S -->
            <div class="room-card animate-up delay-3">
                <img src="img/reserve.jpg" alt="Meeting Room" class="room-image animate-up delay-3">
                <div class="room-info">
                    <div class="room-title">CONTAINER ROOM SIZE S</div>
                    <div class="room-capacity">Max capacity 6</div>
                    <div class="room-hours">Available booking hour 10am - 8pm</div>
                    
                    <div class="pricing-table">
                        <div class="pricing-header">Weekday</div>
                        <div class="pricing-header">Weekend & holiday</div>
                        <div class="pricing-cell">hourly 250 ฿</div>
                        <div class="pricing-cell">hourly 350 ฿</div>
                    </div>
                    
                    <ul class="features-list">
                        <li>Free Highspeed WIFI</li>
                        <li>TV screen on request<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;we provide you with 32" TV screen<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;or projector</li>
                        <li>Whiteboard on request</li>
                        <li>Food & Beverage<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;Enjoy your coffee break and/or<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;lunch on request</li>
                    </ul>
                    
                    <div class="special-note">
                        *NOT ALLOWED<br>
                        &nbsp;&nbsp;FOOD AND DRINK FROM OUTSIDE!*
                    </div>
                    
                    <div class="food-beverage">Food & Beverage</div>
                    <div style="font-size: 14px; line-height: 1.4;">
                        Order Food & Drink over 3,000 ฿ get<br>
                        1 hour *FREE!*
                    </div>
                    
                    <button class="reserve-btn animate-up delay-4" onclick="window.location.href='reserveE.php?event=Meeting&room=S'">Reserve</button>
                </div>
            </div>

            <!-- Container Room Size M -->
            <div class="room-card animate-up delay-3">
                <img src="img/meeting.jpg" alt="Meeting Room" class="room-image animate-up delay-3">
                <div class="room-info">
                    <div class="room-title">CONTAINER ROOM SIZE M</div>
                    <div class="room-capacity">Max capacity 12</div>
                    <div class="room-hours">Available booking hour 10am - 8pm</div>
                    
                    <div class="pricing-table">
                        <div class="pricing-header">Weekday</div>
                        <div class="pricing-header">Weekend & holiday</div>
                        <div class="pricing-cell">hourly 500 ฿</div>
                        <div class="pricing-cell">hourly 550 ฿</div>
                    </div>
                    
                    <ul class="features-list">
                        <li>Free Highspeed WIFI</li>
                        <li>TV screen on request<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;we provide you with 32" TV screen<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;or projector</li>
                        <li>Whiteboard on request</li>
                        <li>Food & Beverage<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;Enjoy your coffee break and/or<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;lunch on request</li>
                    </ul>
                    
                    <div class="special-note">
                        *NOT ALLOWED<br>
                        &nbsp;&nbsp;FOOD AND DRINK FROM OUTSIDE!*
                    </div>
                    
                    <div class="food-beverage">Food & Beverage</div>
                    <div style="font-size: 14px; line-height: 1.4;">
                        Order Food & Drink over 3,000 ฿ get<br>
                        1 hour *FREE!*
                    </div>
                    
                    <button class="reserve-btn animate-up delay-4" onclick="window.location.href='reserveE.php?event=Meeting&room=M'">Reserve</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>