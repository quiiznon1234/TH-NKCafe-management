<?php
    session_start();
    $isLoggedIn = isset($_SESSION['username']);

    // อัปเดตสถานะ
    if (isset($_POST['approve_id'])) {
        require_once 'config.php';
        $stmt = $conn->prepare("UPDATE event_cm SET status='ยืนยันแล้ว' WHERE id=?");
        $stmt->bind_param("i", $_POST['approve_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("Location: Admin_event.php");
        exit();
    }

    // ลบข้อมูล
    if (isset($_POST['delete_id'])) {
        require_once 'config.php';
        $stmt = $conn->prepare("DELETE FROM event_cm WHERE id=?");
        $stmt->bind_param("i", $_POST['delete_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("Location: Admin_event.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Reserve event</title>
    <link rel="stylesheet" href="css/admin_event.css">
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
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="Admin_menu.php">Menu</a></li>
                <li><a href="event.php" class="active">Event</a></li>
            </ul>
        </nav>
    </header>

    <h1>ตารางการจองกิจกรรม</h1>
    <table border="1" id="reservedEvent">
        <tr>
            <th>No.</th>
            <th>Event Type</th>
            <th>Name</th>
            <th>Phone number</th>            
            <th>Amount of Guests</th>
            <th>Date</th>
            <th>Time</th>
            <th>Actions</th>
        </tr>
        <?php
            require_once 'config.php';
            $result = $conn->query("SELECT * FROM event_cm ORDER BY id DESC");
            $i = 1;
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['event']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['phonenum']) ?></td>
                <td><?= htmlspecialchars($row['guests']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['date'])) ?></td>
                <td><?= htmlspecialchars($row['time']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] !== 'ยืนยันแล้ว'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                        <button type="submit">ยืนยัน</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                        <button type="submit" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; $conn->close(); ?>
    </table>

    <script src="js/Admin_event.js"></script>        
</body>
</html>
