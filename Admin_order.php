<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
$_SESSION['is_member'] = true;
$isLoggedIn = isset($_SESSION['username']);
$is_member = isset($_SESSION['is_member']) && $_SESSION['is_member'] == true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Orders</title>
    <link rel="stylesheet" href="css/admin_order.css">
</head>
<body>
    <header>
        <div class="logo">Think Cafe</div>
        <nav>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><span><?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="logout.php">Log-out</a></li>
                <?php else: ?>
                    <li><a href="register.php">Sign-up</a></li>
                    <li><a href="login.php">Log-in</a></li>
                <?php endif; ?>
                    <li><a href="checkTables.php">Reservations</a></li>
                <li><a href="home.php">Home</a></li>
                <li><a href="reserveT.php">Reserve</a></li>
                <li><a href="Admin_menu.php">Menu</a></li>
                <li><a href="event.php">Event</a></li>
                <li><a href="Admin_order.php" class="active">Order</a></li>
                <li><a href="Admin_report.php">Report</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Order Management</h1>
        
        <div class="status-filter">
            <button class="filter-btn active">All Orders</button>
            <button class="filter-btn">Pending</button>
            <button class="filter-btn">Cancelled</button>
            <button class="filter-btn">Accepted</button>
            <button class="filter-btn">Completed</button>
        </div>
        
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Table</th>
                    <th>Time</th>
                    <th>Order Details</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <!-- Modal -->
            <div id="editOrderModal" class="modal hidden">
            <div class="modal-content">
                <h2>Edit Order</h2>
                <div id="order-list"></div>
                <div class="order-summary">
                <strong>Total:</strong> <span id="order-total">0.00 ฿</span>
                </div>
                <div class="modal-actions">
                    <button id="save-order-edit">Save</button>
                    <button id="cancel-order-btn">Cancel Order</button>
                    <button id="close-modal">Cancel</button>
                </div>
            </div>
            </div>

            <?php
            require_once 'config.php';
            $result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
            ?>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-status="<?= strtolower($row['status']) ?>">
                    <td>#ORD-<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= !empty($row['table_id']) ? htmlspecialchars($row['table_id']) : '<span style="color:#888;">-</span>' ?></td>
                    <td>
                        <div><?= date('d M Y', strtotime($row['created_at'])) ?></div>
                        <div class="timestamp"><?= date('H:i A', strtotime($row['created_at'])) ?></div>
                    </td>
                    <td>
                    <div class="order-details">
                        <?php
                        $lines = explode("\n", trim($row['order_details']));
                        $grouped = [];

                        foreach ($lines as $line) {
                            if (preg_match('/^(.*?)(?:\s*\[(.*?)\])?\s*-\s*(\d+(?:\.\d+)?)\s*฿$/u', trim($line), $matches)) {
                                $item = $matches[1];         // เมนู (ชื่อ + group/qty)
                                $comment = $matches[2] ?? ''; // ✅ คอมเมนต์ถ้ามี
                                $price = floatval($matches[3]);
                                $key = $item . '|' . $price;

                                if (!isset($grouped[$key])) {
                                    $grouped[$key] = [
                                        'name' => $item,
                                        'price' => $price,
                                        'qty' => 1,
                                        'comment' => $comment
                                    ];
                                } else {
                                    $grouped[$key]['qty']++;
                                }
                            }
                        }

                        foreach ($grouped as $g) {
                            $lineTotal = $g['price'] * $g['qty'];
                            echo htmlspecialchars($g['name']);
                            if ($g['qty'] > 1) {
                                echo " *{$g['qty']}";
                            }
                            if (!empty($g['comment'])) {
                                echo "<br><small style='color:#666;'>&nbsp;&nbsp;📝 " . htmlspecialchars($g['comment']) . "</small>";
                            }
                            echo " - " . number_format($lineTotal, 2) . " ฿<br>";
                        }
                        ?>
                    </div>
                    </td>
                    <td>฿<?= number_format($row['total'], 2) ?></td>
                    <td><span class="badge badge-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                            <button class="order-btn edit-order-btn" data-order-id="<?= $row['id'] ?>">Edit</button>
                            <button class="order-btn accept-btn" data-order-id="<?= $row['id'] ?>">Accept</button>
                        <?php else: ?>
                            <div class="order-btn accepted"><i>✓</i> Accepted</div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            <td>
                <?php if ($row['status'] === 'Pending'): ?>
                    <div class="order-actions">
                        <button class="order-btn edit-order-btn" data-order-id="<?= $row['id'] ?>">Edit</button>
                        <button class="order-btn accept-btn" data-order-id="<?= $row['id'] ?>">Accept</button>
                    </div>
                <?php elseif ($row['status'] === 'Accepted'): ?>
                    <div class="order-actions">
                        <button class="order-btn checkout-btn" data-order-id="<?= $row['id'] ?>">Checkout</button>
                    </div>
                <?php else: ?>
                    <div class="order-btn completed"><i>✓</i> Completed</div>
                <?php endif; ?>
            </td>
            </tbody>
        </table>
    </div>
    
    <audio id="order-alert-sound" src="sounds/mixkit-correct-answer-reward-952.wav" preload="auto"></audio>
    <script src="js/Admin_order.js"></script>
</body>
</html>