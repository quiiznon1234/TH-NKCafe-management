<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user'; // ค่าเริ่มต้นเป็น user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Reservations</title>
    <link rel="stylesheet" href="css/checktable.css">
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
                <li><a href="checkTables.php" class="active">Reservations</a></li>
                <li><a href="home.php">Home</a></li>
                <li><a href="reserveT.php">Reserve</a></li>
                <li><li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">menu</a></li></li>
                <li><a href="event.php">Event</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="Admin_report.php">Report</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
    <div class="grid-container">
        <div class="grid-item animate-up delay-2" id="reserve">
            <a href="<?php echo ($role === 'admin') ? 'Admin_table.php' : 'user_table.php'; ?>">
            <img src="img/table.jpg" alt="Reserve Icon" class="cover-image">
                <h2>Reserved Table</h2>
                <p>โต็ะที่จอง</p>
            </a>
        </div>
        <div class="grid-item animate-up delay-3" id="menu">
            <a href="<?php echo ($role === 'admin') ? 'Admin_event.php' : 'user_event.php'; ?>">
            <img src="img/event1.jpg" alt="Reserve Icon" class="cover-image">
                <h2>Reserved Event</h2>
                <p>กิจกรรมที่จอง</p>
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