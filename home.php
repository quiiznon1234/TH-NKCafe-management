<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <header class="animate-up delay-1">
        <div class="logo">Think Cafe</div>
        <nav>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><span><?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="Admin_order.php">Order</a></li>
                    <li><a href="Admin_report.php">Report</a></li>
                <?php endif; ?>
                    <li><a href="logout.php">Log-out</a></li>
                    <li><a href="checkTables.php">Reservations</a></li>
                <?php else: ?>
                    <li><a href="register.php">Sign-up</a></li>
                    <li><a href="login.php">Log-in</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main class="animate-up delay-2">
        <div class="grid-container">
            <div class="grid-item animate-up delay-1" id="reserve">
                <a href="reserveT.php">
                <img src="img/reserve.JPG" alt="Reserve Icon" class="cover-image">
                    <h2>Reserve</h2>
                    <p>จองโต็ะ</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-2" id="menu">
                <a href="<?php echo ($isLoggedIn && $_SESSION['username'] === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">
                    <img src="img/menu.JPG" alt="menu Icon" class="cover-image">
                    <h2>Menu</h2>
                    <p>เมนู</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-3" id="event">
                <a href="event.php">
                <img src="img/event.JPG" alt="event Icon" class="cover-image">
                    <h2>Event</h2>
                    <p>จัดงาน</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-4" id="promotion">
                <a href="<?php echo ($isLoggedIn && $_SESSION['username'] === 'admin') ? 'Admin_promotion.php' : 'user_promotion.php'; ?>">
                <img src="img/promotion.JPG" alt="promotion Icon" class="cover-image">
                    <h2>Promotion</h2>
                    <p>โปรโมชัน</p>
                </a>
            </div>
        </div>
    </main>

    <footer class="animate-up delay-4">
        <div class="contact">
            ช่องทางติดต่อ<br>
            LINE ID: XXXXXXXX<br>
            Facebook Page: XXXXXXXX<br>
            Instagram: XXXXXXXX<br>
            เบอร์ติดต่อ: XXX-XXX-XXXX
        </div>
    </footer>
</body>
</html>