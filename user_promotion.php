<?php
session_start();

$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';

$promotions = [];
if (file_exists('promotion.json')) {
    $promotions = json_decode(file_get_contents('promotion.json'), true);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Promotion</title>
    <link rel="stylesheet" href="css/user_promotion.css">
</head>
<body>
    <header>
        <div class="logo">◯Think Cafe</div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">Menu</a></li>
                <li><a href="#" class="active">Event</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><span><?php echo htmlspecialchars($username); ?></span></li>
                    <li><a href="logout.php">Log-out</a></li>
                <?php else: ?>
                    <li><a href="register.php">Sign-up</a></li>
                    <li><a href="login.php">Log-in</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Current Promotion</h2>
        <div class="promotion-list">
            <?php if (!empty($promotions)): ?>
                <?php foreach ($promotions as $promo): ?>
                    <div class="promo-card">
                        <img src="<?= htmlspecialchars($promo['image']) ?>" alt="Promotion Image">
                        <div class="promo-content">
                            <h3><?= htmlspecialchars($promo['title']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($promo['description'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No promotion available.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
