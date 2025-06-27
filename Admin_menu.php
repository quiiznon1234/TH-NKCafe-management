<?php
session_start();
$_SESSION['is_member'] = true;
$isLoggedIn = isset($_SESSION['username']);
$is_member = isset($_SESSION['is_member']) && $_SESSION['is_member'] == true;

// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตัวแปรสำหรับชื่อไฟล์
$newFileName = '';

// บันทึกเมนูใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foodName'])) {
    $foodName = $_POST['foodName'] ?? '';
    $foodPrice = $_POST['foodPrice'] ?? '';
    $menuGroup = $_POST['menu_group'] ?? '';
    $menuType = $_POST['menu_type'] ?? '';
    $drinkType = ($menuGroup === 'Drink') ? ($_POST['drink_type'] ?? null) : null;
    $imagePath = '';

    if (isset($_FILES['foodImg']) && $_FILES['foodImg']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foodImg']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['foodImg']['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $fileExtension;
        $destPath = 'uploads/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $imagePath = $newFileName;
        }
    }

    if ($foodName && $foodPrice && $menuGroup && $menuType && $imagePath) {
        $stmt = $conn->prepare("INSERT INTO menu_cm (image, name, price, menu_group, menu_type, drink_type)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $imagePath, $foodName, $foodPrice, $menuGroup, $menuType, $drinkType);
        $stmt->execute();
        $stmt->close();
         if ($imgFile && file_exists("uploads/$imgFile")) {
        unlink("uploads/$imgFile");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
}

// ลบเมนู
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("SELECT image FROM menu_cm WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imgFile);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM menu_cm WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    if ($imgFile && file_exists("uploads/$imgFile")) {
        unlink("uploads/$imgFile");
    }

    header("Location: Admin_menu.php");
    exit();
}

// ดึงเมนู New (5 รายการล่าสุดจาก Food และ Drink)
$newMenus = [];
$result = $conn->query("SELECT * FROM menu_cm WHERE menu_group IN ('Food', 'Drink', 'Dessert', 'Alcohol') ORDER BY id DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $newMenus[] = $row;
}

// ดึงเมนู Best Sale (5 รายการที่สั่งมากที่สุด)
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

// เรียงลำดับและเลือกชื่อที่ขายดีสุดไม่ซ้ำ 5 รายการ
arsort($bestSaleMap);
$topMenuNames = array_slice(array_keys($bestSaleMap), 0, 5);

// ดึงเมนูที่ชื่อใกล้เคียงจาก menu_cm
$bestSaleMenus = [];
if (!empty($topMenuNames)) {
    $placeholders = implode(',', array_fill(0, count($topMenuNames), '?'));
    $stmt = $conn->prepare("SELECT * FROM menu_cm WHERE LOWER(TRIM(name)) IN ($placeholders)");
    
    $stmt->bind_param(str_repeat('s', count($topMenuNames)), ...$topMenuNames);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $nameKey = strtolower(trim($row['name']));
        if (isset($bestSaleMap[$nameKey])) {
            $row['total_ordered'] = $bestSaleMap[$nameKey];
            $bestSaleMenus[] = $row;
        }
    }
    $stmt->close();
}

// ดึงเมนูทั้งหมด แยกตาม group
$menuByGroup = ['Food' => [], 'Drink' => [], 'Dessert' => [], 'Alcohol' => []];
$result = $conn->query("SELECT * FROM menu_cm WHERE menu_group != 'Recommend' ORDER BY id DESC");
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
    <title>Admin Menu</title>
    <link rel="stylesheet" href="css/food.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
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
            <li><a href="user_menu.php" class="active">Menu</a></li>
            <li><a href="event.php">Event</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="Admin_order.php">Order</a></li>
            <li><a href="Admin_report.php">Report</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="floating-btns">
    <button class="icon-button" id="openOrderBtn" title="Cart">
    <h3><i class="bi bi-basket"></i></h3>
    </button>
    </div>
    <section>
  <div class="category-tabs">
    <button class="tab-button active" data-main="recommend">Recommend</button>
    <button class="tab-button" data-main="food">Food</button>
    <button class="tab-button" data-main="drink">Drink</button>
    <button class="tab-button" data-main="dessert">Dessert</button>
    <button class="tab-button" data-main="alcohol">Alcohol</button>
  </div>

  

 <div class="subcategory-tabs" id="subcategoryTabs"></div>
    <!-- ตัวอย่าง: จะถูกใส่โดย JS -->
     
    <div id="menuContainer">
    <!-- Recommend Section - Dynamic -->
    <!-- New Items -->
    <ul class="menu-list" data-main="recommend" data-sub="new">
        <?php if (empty($newMenus)): ?>
            <li style="text-align: center; padding: 20px; color: #666;">
                <p>No new items available</p>
            </li>
        <?php else: ?>
            <?php foreach ($newMenus as $food): ?>
                <li>
                    <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
                    <strong><?= htmlspecialchars($food['name']) ?></strong><br>
                    <small style="color: #666;"><?= htmlspecialchars($food['menu_group']) ?> - <?= htmlspecialchars($food['menu_type']) ?></small><br>
                    <?= htmlspecialchars($food['price']) ?> ฿
                    <div class="button-row">
                    <form method="post" action="Admin_menu.php" onsubmit="return confirm('Delete this item?');">
                        <input type="hidden" name="delete_id" value="<?= $food['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                    <button class="add-btn"
                        data-name="<?= htmlspecialchars($food['name']) ?>"
                        data-price="<?= htmlspecialchars($food['price']) ?>"
                        data-group="<?= htmlspecialchars($food['menu_group']) ?>">
                        Add to Cart
                    </button>
                    <button class="toggle-menu-btn" data-id="<?= $food['id'] ?>" data-status="open" data-is-closed="<?= $food['is_closed'] ?>">ปิดเมนู</button>
                    <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <!-- Best Sale Items -->
    <ul class="menu-list hidden" data-main="recommend" data-sub="best sale">
        <?php if (empty($bestSaleMenus)): ?>
            <li style="text-align: center; padding: 20px; color: #666;">
                <p>No best selling items available</p>
            </li>
        <?php else: ?>
            <?php foreach ($bestSaleMenus as $food): ?>
                <li>
                    <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
                    <strong><?= htmlspecialchars($food['name']) ?></strong><br>
                    <small style="color: #666;"><?= htmlspecialchars($food['menu_group']) ?> - <?= htmlspecialchars($food['menu_type']) ?></small><br>
                    <?= htmlspecialchars($food['price']) ?> ฿
                    <?php if (isset($food['total_ordered']) && $food['total_ordered'] > 0): ?>
                        <small style="color: #007bff; font-weight: bold;">🔥 Ordered <?= $food['total_ordered'] ?> times</small><br>
                    <?php endif; ?>
                    <div class="button-row">
                    <form method="post" action="Admin_menu.php" onsubmit="return confirm('Delete this item?');">
                        <input type="hidden" name="delete_id" value="<?= $food['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                    <button class="add-btn"
                        data-name="<?= htmlspecialchars($food['name']) ?>"
                        data-price="<?= htmlspecialchars($food['price']) ?>"
                        data-group="<?= htmlspecialchars($food['menu_group']) ?>">
                        Add to Cart
                    </button>
                    <button class="toggle-menu-btn" data-id="<?= $food['id'] ?>" data-status="open" data-is-closed="<?= $food['is_closed'] ?>">ปิดเมนู</button>
                    <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <!-- Regular Menu Items (Food, Dessert, Alcohol) -->
    <?php foreach ($menuByGroup as $group => $items): ?>
        <?php if (strtolower($group) === 'drink') continue; // ข้ามเมนู drink ?>

        <?php
        $groupedByType = [];

        foreach ($items as $food) {
            $main = strtolower($food['menu_group']);
            $sub  = strtolower($food['menu_type']);
            $key  = $main . '_' . $sub;

            if (!isset($groupedByType[$key])) {
                $groupedByType[$key] = [];
            }
            $groupedByType[$key][] = $food;
        }

        $allSubtypes = $subcategories[strtolower($group)] ?? [];
        foreach ($allSubtypes as $subtype) {
            $key = strtolower($group) . '_' . strtolower($subtype);
            if (!isset($groupedByType[$key])) {
                $groupedByType[$key] = []; // สร้างช่องว่างไว้ให้แสดง ul
            }
        }
        ?>

        <?php foreach ($groupedByType as $key => $foods): ?>
            <?php
                $main = explode('_', $key)[0];
                $sub  = explode('_', $key)[1];
            ?>
            <ul class="menu-list hidden"
                data-main="<?= $main ?>"
                data-sub="<?= $sub ?>">

                 <div class="add-card" style="margin-bottom: 20px; cursor: pointer;">
                    <div class="add-icon">+</div>
                    <div>Add Menu</div>
                </div>

                <?php foreach ($foods as $food): ?>
                    <li>
                        <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Image">
                        <strong><?= htmlspecialchars($food['name']) ?></strong><br>
                        <?= htmlspecialchars($food['price']) ?> ฿
                        <div class="button-row">
                        <form method="post" action="Admin_menu.php" onsubmit="return confirm('Delete this item?');">
                            <input type="hidden" name="delete_id" value="<?= $food['id'] ?? $item['id'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                        <button class="add-btn"
                            data-name="<?= htmlspecialchars($food['name'] ?? $item['name']) ?>"
                            data-price="<?= htmlspecialchars($food['price'] ?? $item['price']) ?>"
                            data-group="<?= htmlspecialchars($food['menu_group'] ?? $item['menu_group']) ?>">
                            Add to Cart
                        </button>
                    <button class="toggle-menu-btn" data-id="<?= $food['id'] ?>" data-status="open" data-is-closed="<?= $food['is_closed'] ?>">ปิดเมนู</button>
                    <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                    </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endforeach; ?>
    
    <!-- Drink Menus -->
    <?php foreach ($drinkMenusByType as $sub => $types): ?>
    <div class="menu-list hidden" data-main="drink" data-sub="<?= $sub ?>">
        <div class="add-card" style="margin: 10px 0 20px; cursor: pointer;">
            <div class="add-icon">+</div>
            <div>Add Menu</div>
        </div>

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
                                <form method="post" action="Admin_menu.php" onsubmit="return confirm('Delete this item?');">
                                    <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                                <button class="add-btn" 
                                    data-name="<?= htmlspecialchars($item['name']) ?>" 
                                    data-price="<?= htmlspecialchars($item['price']) ?>"
                                    data-group="<?= htmlspecialchars($item['menu_group']) ?>">
                                    Add to Cart
                                </button>
                                <button class="toggle-menu-btn" data-id="<?= $food['id'] ?>" data-status="open" data-is-closed="<?= $food['is_closed'] ?>">ปิดเมนู</button>
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

        <div id="drinkRows" class="drink-rows hidden">
            <div class="add-card" style="margin-bottom: 20px; cursor: pointer;">
            <div class="add-icon">+</div>
            <div>Add Menu</div>
        </div>
        <?php foreach ($drinkMenus as $drinkType => $items): ?>
        <div class="drink-row">
            <h3><?= htmlspecialchars($drinkType) ?></h3>

            <div class="drink-scroll">
                <?php foreach ($items as $item): ?>
                    <div class="drink-card">
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
                        <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="price"><?= htmlspecialchars($item['price']) ?> ฿</div>
                       <div class="card-actions">
                        <form method="post" action="Admin_menu.php" onsubmit="return confirm('Delete this item?');">
                            <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                        <button class="add-btn" 
                            data-name="<?= htmlspecialchars($item['name']) ?>" 
                            data-price="<?= htmlspecialchars($item['price']) ?>"
                            data-group="<?= htmlspecialchars($item['menu_group']) ?>">
                            Add to Cart
                        </button>
                        <button class="toggle-menu-btn" data-id="<?= $food['id'] ?>" data-status="open" data-is-closed="<?= $food['is_closed'] ?>">ปิดเมนู</button>
                        <span class="soldout-label" style="display:none;color:red;font-weight:bold;">หมด</span>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
  </section>
</main>

<!-- Modal Form -->
<div id="formModal" class="form-modal">
        <div class="form-modal-content">
            <span class="close">&times;</span>
            <h2>Add New Menu</h2>
            <form action="Admin_menu.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="foodImg">Menu Image</label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-display" id="fileDisplay">
                            <div class="upload-icon">📸</div>
                            <div class="upload-text">Click to upload image</div>
                            <img class="file-preview" id="imagePreview" alt="Preview">
                        </div>
                        <input type="file" id="foodImg" name="foodImg" accept="image/*" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foodName">Menu Name</label>
                    <input type="text" id="foodName" name="foodName" placeholder="Enter menu name" required>
                </div>

                <div class="form-group">
                    <label for="foodPrice">Price (฿)</label>
                    <input type="text" id="foodPrice" name="foodPrice" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="menu_group">Menu Group</label>
                    <select id="menu_group" name="menu_group" required>
                        <option value="">Choose a category</option>
                        <option value="Food">Food</option>
                        <option value="Drink">Drink</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Alcohol">Alcohol</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="menu_type">Menu Type</label>
                    <select id="menu_type" name="menu_type" required>
                        <option value="">Select menu type</option>
                    </select>
                </div>

                <div class="form-group" id="drink_type_group" style="display:none;">
                    <label for="drink_type">Drink Temperature</label>
                    <select id="drink_type" name="drink_type">
                        <option value="">Choose temperature</option>
                        <option value="Hot">Hot</option>
                        <option value="Iced">Iced</option>
                        <option value="Smoothie">Smoothie</option>
                    </select>
                </div>

                <button type="submit" id="add-btn">Add Menu Item</button>
            </form>
        </div>
    </div>

<!-- Order Modal -->
<div id="orderModal" class="modal">
  <div class="order-modal-content">
    <span class="close-order">&times;</span>
    <h2>Your Orders</h2>

    <div class="form-group" style="margin: 10px 0;">
      <label for="tableId" style="display:block; text-align:left; font-weight: bold;">Table Number</label>
      <input type="number" id="tableId" name="table_id" min="1" placeholder="Enter table number" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;" />
    </div>

    <div class="order-scroll-container">
      <div id="orderContent" class="order-list"></div>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script src="js/menu.js"></script>
</body>
</html>