<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Reserved table</title>
    <link rel="stylesheet" href="css/user_table.css">
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
                <li><a href="checkTables.php" class="active">Reservations</a></li>
                <li><a href="home.php">Home</a></li>
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="user_menu.php">Menu</a></li>
                <li><a href="event.php">Event</a></li>
            </ul>
        </nav>
    </header>

    <h1 class="animate-up delay-1">ตารางการจองโต๊ะ</h1>
    <table border="1" id="reservedTable" class="animate-up delay-2">
        <tr>
            <th>No.</th>
            <th>username</th>
            <th>phone number</th>            
            <th>Seats</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Cancel</th>
        </tr>

        <?php
            // เชื่อมต่อฐานข้อมูล
            require_once 'config.php';

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // ดึงข้อมูลการจอง
            $sql = "SELECT id, username, phonenum, seats, date, time, status FROM table_cm WHERE username = ? ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0):
                $i = 1;
                while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['phonenum']); ?></td>
                <td><?php echo htmlspecialchars($row['seats']); ?></td>
                <td><?= date('d-m-Y', strtotime($row['date'])) ?></td>
                <td><?php echo htmlspecialchars($row['time']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] !== 'ยกเลิก'): ?>
                        <form method="post" action="user_reservation_cancel.php" onsubmit="return confirm('ยืนยันการยกเลิกการจอง?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit">ยกเลิก</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php
            endwhile;
            else:
        ?>
            <tr><td colspan="5">ไม่มีข้อมูลการจอง</td></tr>
        <?php endif;
            $stmt->close();
            $conn->close();
        ?>
    </table>
</body>
</html>
