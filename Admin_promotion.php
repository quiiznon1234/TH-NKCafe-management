<?php
session_start();

$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['promotionImg']['name']);
    $description = $_POST['proDetail'];
    $title = $_POST['proTitle'];

    if (move_uploaded_file($_FILES['promotionImg']['tmp_name'], $uploadFile)) {
        // โหลดไฟล์เก่า ถ้ามี
        $existingPromos = [];
        if (file_exists('promotion.json')) {
            $existingPromos = json_decode(file_get_contents('promotion.json'), true);
        }

        // เพิ่มข้อมูลใหม่
        $newPromo = [
            'image' => $uploadFile,
            'title' => $title,
            'description' => $description
        ];
        $existingPromos[] = $newPromo;

        file_put_contents('promotion.json', json_encode($existingPromos, JSON_PRETTY_PRINT));
        echo "Promotion saved successfully.";
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin_promotion.css">
    <title>Admin Promotion</title>
</head>
<body>
    <header>
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
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">Menu</a></li>
                <li><a href="event.php">Event</a></li>
            </ul>
        </nav>
    </header>

    <!-- Promotion Form -->
    <form action="admin_promotion.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="proTitle">Title</label>
            <input type="text" id="proTitle" name="proTitle" required>
        </div>
        <div>
            <label for="promotionImg" class="custom-file-upload">+</label>
            <input type="file" id="promotionImg" name="promotionImg" required>
        </div>
        <div>
            <label for="proDetail">Description</label>
            <textarea id="proDetail" name="proDetail" rows="4" required></textarea>
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
