<?php
if (isset($_GET['table'])) {
    session_start();
    $_SESSION['table_id'] = htmlspecialchars($_GET['table']);
} else {
    session_start();
}
$_SESSION['is_member'] = true;

$isLoggedIn = isset($_SESSION['username']);

// ตรวจสอบสถานะสมาชิก
$is_member = isset($_SESSION['is_member']) && $_SESSION['is_member'] == true;

// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

$conn = new mysqli(
    $DB_CONFIG['host'],
    $DB_CONFIG['user'],
    $DB_CONFIG['pass'],
    $DB_CONFIG['name'],
    $DB_CONFIG['port']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ✅ เมนู New (5 รายการล่าสุดจาก Food และ Drink)
$newMenus = [];
$resNew = $conn->query("SELECT * FROM menu_cm WHERE menu_group IN ('Food', 'Drink') ORDER BY id DESC LIMIT 5");
while ($row = $resNew->fetch_assoc()) {
    $newMenus[] = $row;
}

// ✅ เมนู Best Sale (จาก order_details ของ order ที่ status = Accepted)
$bestSaleMap = [];
$res = $conn->query("SELECT order_details FROM orders WHERE status = 'Completed'");
while ($row = $res->fetch_assoc()) {
    $lines = explode("\n", $row['order_details']);
    foreach ($lines as $line) {
        if (preg_match('/^(.*?)\s*(?:\((.*?)\))?\s*\*?(\d+)?\s*-\s*[\d.,]+\s*฿$/u', trim($line), $m)) {
            $name = strtolower(trim($m[1]));
            $qty = isset($m[3]) && $m[3] ? (int)$m[3] : 1;
            $bestSaleMap[$name] = ($bestSaleMap[$name] ?? 0) + $qty;
        }
    }
}
arsort($bestSaleMap);
$topMenuNames = array_slice(array_keys($bestSaleMap), 0, 5);

$bestSaleMenus = [];
if (!empty($topMenuNames)) {
    $placeholders = implode(',', array_fill(0, count($topMenuNames), '?'));
    $stmt = $conn->prepare("SELECT * FROM menu_cm WHERE LOWER(TRIM(name)) IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($topMenuNames)), ...$topMenuNames);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $key = strtolower(trim($row['name']));
        if (isset($bestSaleMap[$key])) {
            $row['total_ordered'] = $bestSaleMap[$key];
            $bestSaleMenus[] = $row;
        }
    }
    $stmt->close();
}

// ดึงเมนูทั้งหมด แยกตาม group
$menuByGroup = ['Recommend' => [],'Food' => [], 'Drink' => [], 'Dessert' => [], 'Alcohol' => []];
$result = $conn->query("SELECT * FROM menu_cm ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $group = $row['menu_group'];
    if (isset($menuByGroup[$group])) {
        $menuByGroup[$group][] = $row;
    }
}
$subcategories = [
    'recommend'=> ["New", "Best Sale"],
    'food' => ["pizza", "pasta", "Burger", "main"],
    'dessert' => ["cake", "kaki", "toast", "crepe"],
    'alcohol' => ["beer", "wine"]
];
$drinkMenus = ['Hot' => [], 'Iced' => [], 'Smoothie' => []];
$result = $conn->query("SELECT * FROM menu_cm WHERE menu_group = 'Drink' ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $type = $row['drink_type'];
    if (isset($drinkMenus[$type])) {
        $drinkMenus[$type][] = $row;
    }
}
$drinkMenusGrouped = [];

// ดึงเฉพาะเมนู drink ทั้งหมด
$result = $conn->query("SELECT * FROM menu_cm WHERE menu_group = 'Drink' ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $menuType = strtolower($row['menu_type']);     // เช่น coffee, milk, soda
    $drinkType = strtolower($row['drink_type']);   // เช่น hot, iced, smoothie

    if (!isset($drinkMenusGrouped[$menuType])) {
        $drinkMenusGrouped[$menuType] = [];
    }
    if (!isset($drinkMenusGrouped[$menuType][$drinkType])) {
        $drinkMenusGrouped[$menuType][$drinkType] = [];
    }
    $drinkMenusGrouped[$menuType][$drinkType][] = $row;
}
$drinkMenusByType = [
    'coffee' => ['Hot' => [], 'Iced' => [], 'Smoothie' => []],
    'milk'   => ['Hot' => [], 'Iced' => [], 'Smoothie' => []],
    'fruit'   => ['Hot' => [], 'Iced' => [], 'Smoothie' => []],
    'tea'    => ['Hot' => [], 'Iced' => [], 'Smoothie' => []],
];

$result = $conn->query("SELECT * FROM menu_cm WHERE menu_group = 'Drink' ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $menuType = strtolower($row['menu_type']);
    $drinkType = $row['drink_type'];

    if (isset($drinkMenusByType[$menuType][$drinkType])) {
        $drinkMenusByType[$menuType][$drinkType][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Think Cafe - Menu</title>
    <link rel="stylesheet" href="css/user_menu.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>
<body>

<header class="animate-up delay-1">
    <div class="logo">Think Cafe</div>
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><span><?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <?php endif; ?>
                <li><a href="logout.php">Log-out</a></li>
            <?php else: ?>
                <li><a href="register.php">Sign-up</a></li>
                <li><a href="login.php">Log-in</a></li>
            <?php endif; ?>
                <li><a href="checkTables.php">Reservations</a></li>
            <li><a href="home.php">Home</a></li>
            <li><a href="reserveT.php">Reserve</a></li>
            <li><a href="user_menu.php" class="active">Menu</a></li>
            <li><a href="event.php">Event</a></li>
        </ul>
    </nav>
</header>

<main>
  
  <!-- ปุ่มกลุ่มขวาล่าง -->
  <div class="floating-btns">
    <button class="icon-button" id="openOrderBtn" title="Cart">
        <i class="bi bi-basket"></i>
    </button>
    <button class="icon-button history-btn" id="orderHistoryBtn" title="Order History">
        <i class="bi bi-clock-history"></i>
    </button>
  </div>
    <div id="toast">เพิ่มเมนูแล้ว</div>
  <section>
    <div class="category-tabs animate-up delay-2">
      <button class="tab-button active" data-main="recommend">Recommend</button>
      <button class="tab-button" data-main="food">Food</button>
      <button class="tab-button" data-main="drink">Drink</button>
      <button class="tab-button" data-main="dessert">Dessert</button>
      <button class="tab-button" data-main="alcohol">Alcohol</button>
    </div>

    <div class="subcategory-tabs animate-up delay-3" id="subcategoryTabs"></div>

    <div id="menuContainer">

      <!-- ✅ NEW MENUS (จาก Food & Drink ล่าสุด) -->
      <ul class="menu-list" data-main="recommend" data-sub="new">
        <?php foreach ($newMenus as $food): ?>
          <li>
            <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
            <strong><?= htmlspecialchars($food['name']) ?></strong><br>
            <small><?= htmlspecialchars($food['menu_group']) ?> - <?= htmlspecialchars($food['menu_type']) ?></small><br>
            <?= htmlspecialchars($food['price']) ?> ฿
            <div class="button-row">
              <button class="add-btn"
                data-name="<?= htmlspecialchars($food['name']) ?>"
                data-price="<?= htmlspecialchars($food['price']) ?>"
                data-group="<?= htmlspecialchars($food['menu_group']) ?>"
                data-is-closed="<?= $food['is_closed'] ?>">
                Add to Cart
              </button>
              <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- ✅ BEST SALE MENUS -->
      <ul class="menu-list hidden" data-main="recommend" data-sub="best sale">
        <?php foreach ($bestSaleMenus as $food): ?>
          <li>
            <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
            <strong><?= htmlspecialchars($food['name']) ?></strong><br>
            <small><?= htmlspecialchars($food['menu_group']) ?> - <?= htmlspecialchars($food['menu_type']) ?></small><br>
            <?= htmlspecialchars($food['price']) ?> ฿
            <?php if (isset($food['total_ordered'])): ?>
              <div style="color: #555; font-size: 12px;">🔥 <?= $food['total_ordered'] ?> orders</div>
            <?php endif; ?>
            <div class="button-row">
              <button class="add-btn"
                data-name="<?= htmlspecialchars($food['name']) ?>"
                data-price="<?= htmlspecialchars($food['price']) ?>"
                data-group="<?= htmlspecialchars($food['menu_group']) ?>"
                data-is-closed="<?= $food['is_closed'] ?>">
                Add to Cart
              </button>
              <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- ✅ เมนูกลุ่ม Food / Dessert / Alcohol -->
      <?php foreach ($menuByGroup as $group => $items): ?>
        <?php if (strtolower($group) === 'drink' || strtolower($group) === 'recommend') continue; ?>
        <?php
          $groupedByType = [];
          foreach ($items as $food) {
              $main = strtolower($food['menu_group']);
              $sub  = strtolower($food['menu_type']);
              $key  = $main . '_' . $sub;
              $groupedByType[$key][] = $food;
          }

          $allSubtypes = $subcategories[strtolower($group)] ?? [];
          foreach ($allSubtypes as $subtype) {
              $key = strtolower($group) . '_' . strtolower($subtype);
              if (!isset($groupedByType[$key])) {
                  $groupedByType[$key] = []; // ช่องว่างไว้
              }
          }
        ?>

        <?php foreach ($groupedByType as $key => $foods): ?>
          <?php
            $main = explode('_', $key)[0];
            $sub = explode('_', $key)[1];
          ?>
          <ul class="menu-list hidden" data-main="<?= $main ?>" data-sub="<?= $sub ?>">
            <?php foreach ($foods as $food): ?>
              <li>
                <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
                <strong><?= htmlspecialchars($food['name']) ?></strong><br>
                <?= htmlspecialchars($food['price']) ?> ฿
                <div class="button-row">
                  <button class="add-btn"
                    data-name="<?= htmlspecialchars($food['name']) ?>"
                    data-price="<?= htmlspecialchars($food['price']) ?>"
                    data-group="<?= htmlspecialchars($food['menu_group']) ?>"
                    data-is-closed="<?= $food['is_closed'] ?>">
                    Add to Cart
                  </button>
                  <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endforeach; ?>
      <?php endforeach; ?>

      <!-- ✅ DRINK แยกตามหมวด -->
      <?php foreach ($drinkMenusByType as $sub => $types): ?>
        <div class="menu-list hidden" data-main="drink" data-sub="<?= $sub ?>">
          <?php foreach (['Hot', 'Iced', 'Smoothie'] as $drinkType): ?>
            <?php if (!empty($types[$drinkType])): ?>
              <div class="drink-row">
                <h3><?= htmlspecialchars($drinkType) ?></h3>
                <div class="drink-scroll">
                  <?php foreach ($types[$drinkType] as $item): ?>
                    <div class="drink-card">
                      <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
                      <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                      <div class="price"><?= htmlspecialchars($item['price']) ?> ฿</div>
                      <button class="add-btn" 
                          data-name="<?= htmlspecialchars($item['name']) ?>" 
                          data-price="<?= htmlspecialchars($item['price']) ?>"
                          data-group="<?= htmlspecialchars($item['menu_group']) ?>"
                          data-is-closed="<?= $food['is_closed'] ?>">
                          Add to Cart
                      </button>
                      <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<!-- Order Modal -->
<div id="orderModal" class="modal">
  <div class="order-modal-content">
    <span class="close-order">&times;</span>
    <h2>Your Orders</h2>
            <?php if (isset($_SESSION['table_id'])): ?>
              <div style="text-align:center; font-weight:bold; margin-top:10px; margin-bottom:10px; color: #333;">
                TB : <?= htmlspecialchars($_SESSION['table_id']) ?>
              </div>
            <?php endif; ?>
    <!-- 🔽 รายการ scroll ได้ -->
    <div class="order-scroll-container">
      <div id="orderContent" class="order-list"></div>
    </div>
  </div>
</div>

<!-- Order History Modal -->
<div id="historyModal" class="modal">
  <div class="order-modal-content" style="position: relative;">
    <span class="close-history">&times;</span>
    <h2>Order History</h2>
    <div class="order-scroll-container">
      <div id="historyContent"></div>
    </div>
  </div>
</div>

<script>
  window.TABLE_ID = "<?php echo isset($_SESSION['table_id']) ? htmlspecialchars($_SESSION['table_id'], ENT_QUOTES) : ''; ?>";
</script>
<!-- JavaScript -->
<script src="js/user_menu.js"></script>
<script src="js/toast.js"></script>

</body>
</html>