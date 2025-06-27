<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
$username = $_SESSION['username'];
require_once 'config.php';

$result = $conn->query("SELECT * FROM orders WHERE customer_name = '$username' ORDER BY created_at DESC");
?>
<h2>ประวัติคำสั่งซื้อ</h2>
<?php while ($row = $result->fetch_assoc()): 
  var_dump($row['order_details']);?>
  <div>
    <strong>เวลา:</strong> <?= $row['created_at'] ?> |
    <strong>รวม:</strong> <?= number_format($row['total'], 2) ?> ฿ |
    <strong>สถานะ:</strong> <?= $row['status'] ?>
    <br>
    <pre><?= htmlspecialchars($row['order_details'] ?? '-') ?></pre>
  </div>
<?php endwhile; ?>
