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
    <title>Think Cafe - Event</title>
    <link rel="stylesheet" href="css/event.css">
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
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">Menu</a></li>
                <li><a href="event.php" class="active">Event</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="grid-container">
            <div class="grid-item animate-up delay-2" id="wedding">
                <a href="wedding.php">
                <img src="img/wedding.jpeg" alt="Reserve Icon" class="cover-image">
                    <h2>Wedding Space</h2>
                    <p>งานแต่ง</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-2" id="studio">
                <a href="studios.php">
                <img src="img/studio.jpg" alt="Reserve Icon" class="cover-image">
                    <h2>Studio</h2>
                    <p>ห้องสตูดิโอ</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-3" id="meeting">
                <a href="meeting.php">
                <img src="img/reserve.jpg" alt="Reserve Icon" class="cover-image">
                    <h2>Meeting</h2>
                    <p>ห้องประชุม</p>
                </a>
            </div>
            <div class="grid-item animate-up delay-3" id="private-meeting">
                <a href="Pmeeting.php">
                <img src="img/private.jfif" alt="Reserve Icon" class="cover-image">
                    <h2>Private Meeting</h2>
                    <p>ห้องสัมมนา</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
