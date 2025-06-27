<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);

require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลโต๊ะ
$tableReservations = [];
$sqlTable = "SELECT * FROM table_cm";
$resultTable = $conn->query($sqlTable);
if ($resultTable && $resultTable->num_rows > 0) {
    while ($row = $resultTable->fetch_assoc()) {
        $tableReservations[] = $row;
    }
}

// ดึงข้อมูลกิจกรรม
$eventReservations = [];
$sqlEvent = "SELECT * FROM event_cm";
$resultEvent = $conn->query($sqlEvent);
if ($resultEvent && $resultEvent->num_rows > 0) {
    while ($row = $resultEvent->fetch_assoc()) {
        $eventReservations[] = $row;
    }
}

// ยอดนิยม
$topFoods = $topDrinks = $topDesserts = $topAlcohols = [];

// เตรียม lookup table จาก menu_cm - ปรับให้รองรับหลายหมวดหมู่
$menuGroupLookup = [];
$menuResult = $conn->query("SELECT name, menu_group FROM menu_cm");
if ($menuResult && $menuResult->num_rows > 0) {
    while ($menu = $menuResult->fetch_assoc()) {
        $nameKey = strtolower(trim($menu['name']));
        $group = strtolower(trim($menu['menu_group']));
        
        // ถ้าเมนูนี้ยังไม่มีในตาราง หรือ หมวดหมู่ปัจจุบันไม่ใช่ Recommend
        if (!isset($menuGroupLookup[$nameKey]) || $group !== 'recommend') {
            // ถ้าหมวดหมู่เป็น recommend ให้ข้ามไป (ใช้หมวดหมู่หลักแทน)
            if ($group !== 'recommend') {
                $menuGroupLookup[$nameKey] = $group;
            }
        }
    }
}

// อ่าน order_details ทั้งหมด
$foodCountsByMonth = $drinkCountsByMonth = $dessertCountsByMonth = $alcoholCountsByMonth = [];
$availableMonths = [];
$sql = "SELECT order_details, DATE_FORMAT(created_at, '%Y-%m') as month_year FROM `orders` WHERE status = 'Completed' ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monthYear = $row['month_year'];
        $availableMonths[$monthYear] = $monthYear;
        
        // Initialize arrays if not exists
        if (!isset($foodCountsByMonth[$monthYear])) {
            $foodCountsByMonth[$monthYear] = [];
            $drinkCountsByMonth[$monthYear] = [];
            $dessertCountsByMonth[$monthYear] = [];
            $alcoholCountsByMonth[$monthYear] = [];
        }
        
        $lines = explode("\n", $row['order_details']);
        foreach ($lines as $line) {
            if (preg_match('/^(.*?)\s*(?:\((.*?)\))?\s*(?:\*(\d+))?\s*-\s*[\d.,]+\s*฿$/u', trim($line), $matches)) {
                $nameRaw = trim($matches[1]);
                $qty = isset($matches[3]) && $matches[3] ? (int)$matches[3] : 1;
                $nameKey = strtolower($nameRaw);

                // ดึงหมวดหมู่ของเมนู
                $group = $menuGroupLookup[$nameKey] ?? '';
                
                if (empty($group)) {
                    foreach ($menuGroupLookup as $menuName => $menuGroup) {
                        if (strpos($nameKey, $menuName) !== false || strpos($menuName, $nameKey) !== false) {
                            $group = $menuGroup;
                            break;
                        }
                    }
                }

                // จัดหมวดหมู่ตามประเภทแยกตามเดือน
                switch ($group) {
                    case 'food':
                    case 'main':
                    case 'appetizer':
                        $foodCountsByMonth[$monthYear][$nameRaw] = ($foodCountsByMonth[$monthYear][$nameRaw] ?? 0) + $qty;
                        break;
                    case 'drink':
                    case 'beverage':
                    case 'coffee':
                    case 'tea':
                        $drinkCountsByMonth[$monthYear][$nameRaw] = ($drinkCountsByMonth[$monthYear][$nameRaw] ?? 0) + $qty;
                        break;
                    case 'dessert':
                    case 'sweet':
                    case 'cake':
                        $dessertCountsByMonth[$monthYear][$nameRaw] = ($dessertCountsByMonth[$monthYear][$nameRaw] ?? 0) + $qty;
                        break;
                    case 'alcohol':
                    case 'wine':
                    case 'beer':
                    case 'cocktail':
                        $alcoholCountsByMonth[$monthYear][$nameRaw] = ($alcoholCountsByMonth[$monthYear][$nameRaw] ?? 0) + $qty;
                        break;
                    default:
                        $foodCountsByMonth[$monthYear][$nameRaw] = ($foodCountsByMonth[$monthYear][$nameRaw] ?? 0) + $qty;
                        break;
                }
            }
        }
    }
}

// เรียงลำดับเดือน
krsort($availableMonths);

// เตรียมข้อมูลสำหรับ JavaScript
$monthlyData = [];
foreach ($availableMonths as $monthYear) {
    arsort($foodCountsByMonth[$monthYear]);
    arsort($drinkCountsByMonth[$monthYear]);
    arsort($dessertCountsByMonth[$monthYear]);
    arsort($alcoholCountsByMonth[$monthYear]);
    
    $monthlyData[$monthYear] = [
        'food' => array_slice($foodCountsByMonth[$monthYear], 0, 10, true),
        'drink' => array_slice($drinkCountsByMonth[$monthYear], 0, 10, true),
        'dessert' => array_slice($dessertCountsByMonth[$monthYear], 0, 10, true),
        'alcohol' => array_slice($alcoholCountsByMonth[$monthYear], 0, 10, true)
    ];
}

$foodCounts = $drinkCounts = $dessertCounts = $alcoholCounts = [];
$sql = "SELECT order_details FROM `orders` WHERE status = 'Completed'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lines = explode("\n", $row['order_details']);
        foreach ($lines as $line) {
            // ปรับ regex ให้รองรับรูปแบบต่างๆ
            if (preg_match('/^(.*?)\s*(?:\((.*?)\))?\s*(?:\*(\d+))?\s*-\s*[\d.,]+\s*฿$/u', trim($line), $matches)) {
                $nameRaw = trim($matches[1]);
                $qty = isset($matches[3]) && $matches[3] ? (int)$matches[3] : 1;
                $nameKey = strtolower($nameRaw);

                // ดึงหมวดหมู่ของเมนู
                $group = $menuGroupLookup[$nameKey] ?? '';
                
                // ถ้าไม่พบหมวดหมู่ ให้พยายามหาจากชื่อเมนู
                if (empty($group)) {
                    // ลองหาด้วยชื่อที่คล้ายกัน (กรณีมีเครื่องหมายวรรคตอนหรือตัวอักษรต่างกัน)
                    foreach ($menuGroupLookup as $menuName => $menuGroup) {
                        if (strpos($nameKey, $menuName) !== false || strpos($menuName, $nameKey) !== false) {
                            $group = $menuGroup;
                            break;
                        }
                    }
                }

                // จัดหมวดหมู่ตามประเภท
                switch ($group) {
                    case 'food':
                    case 'main':
                    case 'appetizer':
                        $foodCounts[$nameRaw] = ($foodCounts[$nameRaw] ?? 0) + $qty;
                        break;
                    case 'drink':
                    case 'beverage':
                    case 'coffee':
                    case 'tea':
                        $drinkCounts[$nameRaw] = ($drinkCounts[$nameRaw] ?? 0) + $qty;
                        break;
                    case 'dessert':
                    case 'sweet':
                    case 'cake':
                        $dessertCounts[$nameRaw] = ($dessertCounts[$nameRaw] ?? 0) + $qty;
                        break;
                    case 'alcohol':
                    case 'wine':
                    case 'beer':
                    case 'cocktail':
                        $alcoholCounts[$nameRaw] = ($alcoholCounts[$nameRaw] ?? 0) + $qty;
                        break;
                    default:
                        // ถ้าไม่พบหมวดหมู่ ให้จัดเป็น food เป็นค่าเริ่มต้น
                        $foodCounts[$nameRaw] = ($foodCounts[$nameRaw] ?? 0) + $qty;
                        break;
                }
            }
        }
    }
}

// เรียงลำดับและเลือก top 10
arsort($foodCounts);
arsort($drinkCounts);
arsort($dessertCounts);
arsort($alcoholCounts);

$topFoods = array_slice($foodCounts, 0, 10, true);
$topDrinks = array_slice($drinkCounts, 0, 10, true);
$topDesserts = array_slice($dessertCounts, 0, 10, true);
$topAlcohols = array_slice($alcoholCounts, 0, 10, true);

// รายได้
$incomeReport = [];
$sql = "SELECT DATE(created_at) as date, total FROM `orders` WHERE status = 'Completed'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        $total = floatval($row['total']);
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));

        $incomeReport[$year]['months'][$month]['days'][$date] =
            ($incomeReport[$year]['months'][$month]['days'][$date] ?? 0) + $total;
        $incomeReport[$year]['months'][$month]['total'] =
            ($incomeReport[$year]['months'][$month]['total'] ?? 0) + $total;
        $incomeReport[$year]['total'] =
            ($incomeReport[$year]['total'] ?? 0) + $total;
    }
}

// ออเดอร์ทั้งหมด (เฉพาะสถานะ Accepted)
$orders = [];
$sqlOrder = "SELECT * FROM orders WHERE status = 'Completed' ORDER BY created_at DESC";  // เพิ่มเงื่อนไข status
$resultOrder = $conn->query($sqlOrder);
if ($resultOrder && $resultOrder->num_rows > 0) {
    while ($row = $resultOrder->fetch_assoc()) {
        $orders[] = $row;
    }
}

$monthlyOrders = [];
foreach ($orders as $order) {
    $monthYear = date('Y-m', strtotime($order['created_at']));
    $date = date('Y-m-d', strtotime($order['created_at']));

    if (!isset($monthlyOrders[$monthYear])) {
        $monthlyOrders[$monthYear] = [
            'orders' => [],
            'total' => 0,
            'dailyMenu' => []
        ];
    }

    // สรุปเมนูรายวัน
    $lines = explode("\n", $order['order_details']);
    foreach ($lines as $line) {
        if (preg_match('/^(.*?)\s*(?:\((.*?)\))?\s*(?:\*(\d+))?\s*-\s*[\d.,]+\s*฿$/u', trim($line), $m)) {
            $name = trim($m[1]);
            $qty = isset($m[3]) && $m[3] ? (int)$m[3] : 1;
            
            if (!isset($monthlyOrders[$monthYear]['dailyMenu'][$date][$name])) {
                $monthlyOrders[$monthYear]['dailyMenu'][$date][$name] = 0;
            }
            $monthlyOrders[$monthYear]['dailyMenu'][$date][$name] += $qty;
        }
    }

    $monthlyOrders[$monthYear]['orders'][] = $order;
    $monthlyOrders[$monthYear]['total'] += $order['total'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Think Cafe - Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <div class="logo">Think Cafe</div>
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><span><?= htmlspecialchars($_SESSION['username']); ?></span></li>
                <li><a href="logout.php">Log-out</a></li>
            <?php else: ?>
                <li><a href="login.php">Log-in</a></li>
            <?php endif; ?>
            <li><a href="checkTables.php">Reservations</a></li>
            <li><a href="home.php">Home</a></li>
            <li><a href="reserveT.php">Reserve</a></li>
            <li><a href="Admin_menu.php">Menu</a></li>
            <li><a href="event.php">Event</a></li>
            <li><a href="Admin_order.php">Order</a></li>
            <li><a href="Admin_report_combined.php" class="active">Report</a></li>
        </ul>
    </nav>
</header>

<div class="main-container">
<aside class="sidebar">
    <ul>
        <li><button class="tab-button active" data-target="tableSection">โต๊ะ</button></li>
        <li><button class="tab-button" data-target="eventSection">กิจกรรม</button></li>
        <li><button class="tab-button" data-target="toptenSection">10 อันดับเมนูยอดนิยม</button></li>
        <li><button class="tab-button" data-target="incomeSection">รายได้</button></li>
        <li><button class="tab-button" data-target="menuSection">เมนู</button></li>
    </ul>
</aside>
<div class="content-area">

<!-- โต๊ะ -->
<section id="tableSection" class="section-content">
    <h3>📊 รายงานการจองโต๊ะ</h3>
    
    <?php
        $totalBookings = count($tableReservations);
        $avgSeats = $totalBookings > 0 ? array_sum(array_column($tableReservations, 'seats')) / $totalBookings : 0;

        $timeCounts = [];
        foreach ($tableReservations as $res) {
            $time = $res['time'];
            $timeCounts[$time] = ($timeCounts[$time] ?? 0) + 1;
        }
        arsort($timeCounts);
        $popularTime = key($timeCounts);
    ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>จำนวนการจองทั้งหมด</h4>
            <div class="number"><?= $totalBookings ?></div>
            <small>การจองในระบบ</small>
        </div>
        <div class="stat-card">
            <h4>ที่นั่งเฉลี่ย</h4>
            <div class="number"><?= number_format($avgSeats, 1) ?></div>
            <small>คนต่อโต๊ะ</small>
        </div>
        <div class="stat-card">
            <h4>เวลายอดนิยม</h4>
            <div class="number"><?= $popularTime ?></div>
            <small>เวลาที่จองมากที่สุด</small>
        </div>
        <div class="stat-card">
            <h4>อัตราการจอง</h4>
            <div class="number">100%</div>
            <small>ระบบแสดงข้อมูลจริง</small>
        </div>
    </div>

    <div class="chart-container">
        <h4>📈 กราฟการจองโต๊ะรายวัน</h4>
        <div class="chart-wrapper">
            <canvas id="tableChart"></canvas>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Seats</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableReservations as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['seats']) ?></td>
                    <td><?= htmlspecialchars($r['time']) ?></td>
                    <td><?= htmlspecialchars($r['date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- กิจกรรม -->
<section id="eventSection" class="section-content hidden">
    <h3>🎉 รายงานกิจกรรม</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Event</th>
                    <th>Name</th>
                    <th>Guests</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventReservations as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['event']) ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['guests']) ?></td>
                    <td><?= htmlspecialchars($r['time']) ?></td>
                    <td><?= htmlspecialchars($r['date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- เมนูยอดนิยม -->
<section id="toptenSection" class="section-content hidden">
    <div class="section-header">
        <h3>🏆 10 อันดับเมนูยอดนิยม</h3>
        
        <!-- Date Range Filter -->
        <div class="date-filter">
            <label for="startDate">📅 วัน/เดือน/ปีที่ :</label>
            <input type="date" id="startDate" onchange="filterByDateRange()">
            <span style="margin: 0 8px;">ถึง</span>
            <input type="date" id="endDate" onchange="filterByDateRange()">
        </div>
    </div>

    <div class="chart-container">
        <h4 id="popularChartTitle">📈 กราฟรวมเมนูยอดนิยม</h4>
        <div class="chart-wrapper"><canvas id="popularChart"></canvas></div>
    </div>

    <div class="menu-grid" id="menuGrid">
        <?php
            function renderTopList($title, $icon, $list, $type = '') {
                // เปลี่ยน onclick ไปที่ div.menu-card (ไม่ใช่แค่ data-type)
                $attr = $type ? " data-type=\"$type\" onclick=\"onMenuCardClick(event, '$type')\"" : "";
                echo "<div class='menu-card'$attr data-category='$type'>";
                echo "<h4>{$icon} {$title}</h4>";
                echo "<div class='menu-list'>";
                $i = 1;
                foreach ($list as $menu => $qty) {
                    echo '<div class="menu-item">';
                    echo '<div style="display:flex; align-items:center; gap:10px;">';
                    echo "<div class='menu-rank'>{$i}</div>";
                    echo "<span>{$menu}</span></div>";
                    echo "<div class='menu-quantity'>{$qty}</div>";
                    echo '</div>';
                    $i++;
                }
                echo '</div>';
                echo '</div>';
            }
            renderTopList("อาหารยอดนิยม", "🍽️", $topFoods, "food");
            renderTopList("เครื่องดื่มยอดนิยม", "🥤", $topDrinks, "drink");
            renderTopList("ของหวานยอดนิยม", "🍰", $topDesserts, "dessert");
            renderTopList("เครื่องดื่มแอลกอฮอล์ยอดนิยม", "🍷", $topAlcohols, "alcohol");
        ?>
    </div>
</section>
<script>
// เตรียมข้อมูล order details ทั้งหมด (พร้อมวันที่)
window.allOrderDetails = <?php
// เตรียมข้อมูล order_details พร้อมวันที่
$orderDetailsWithDate = [];
$sql = "SELECT order_details, created_at FROM `orders` WHERE status = 'Completed'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderDetailsWithDate[] = [
            'order_details' => $row['order_details'],
            'created_at' => $row['created_at']
        ];
    }
}
echo json_encode($orderDetailsWithDate);
?>;

// เตรียม lookup table สำหรับหมวดหมู่เมนู
window.menuGroupLookup = <?php echo json_encode($menuGroupLookup); ?>;

// ฟังก์ชันแปลงวันที่เป็น yyyy-mm-dd
function toDateString(date) {
    if (!date) return '';
    const d = new Date(date);
    if (isNaN(d)) return '';
    return d.toISOString().slice(0, 10);
}

// ฟังก์ชันกรองข้อมูลตามช่วงวันที่
function filterByDateRange() {
    const startDate = toDateString(document.getElementById('startDate').value);
    const endDate = toDateString(document.getElementById('endDate').value);
    const currentType = window.currentChartType || 'food';

    // ถ้าไม่ได้เลือกช่วงวันที่ ให้แสดง allTimeTopData
    if (!startDate && !endDate) {
        updateMenuCards(window.allTimeTopData);
        renderPopularChart(currentType, window.allTimeTopData);
        updateChartTitle('เดือนนี้', currentType);
        return;
    }

    // กรอง order_details ตามช่วงวันที่
    const filtered = window.allOrderDetails.filter(item => {
        const d = toDateString(item.created_at);
        if (startDate && d < startDate) return false;
        if (endDate && d > endDate) return false;
        return true;
    });

    // สรุปยอดแต่ละหมวดหมู่
    const menuGroupLookup = window.menuGroupLookup;
    const food = {}, drink = {}, dessert = {}, alcohol = {};
    filtered.forEach(item => {
        const lines = item.order_details.split('\n');
        lines.forEach(line => {
            const m = line.trim().match(/^(.*?)\s*(?:\((.*?)\))?\s*(?:\*(\d+))?\s*-\s*[\d.,]+\s*฿$/u);
            if (m) {
                const nameRaw = m[1].trim();
                const qty = m[3] ? parseInt(m[3]) : 1;
                const nameKey = nameRaw.toLowerCase();
                let group = menuGroupLookup[nameKey] || '';
                if (!group) {
                    for (const menuName in menuGroupLookup) {
                        if (nameKey.includes(menuName) || menuName.includes(nameKey)) {
                            group = menuGroupLookup[menuName];
                            break;
                        }
                    }
                }
                switch (group) {
                    case 'food':
                    case 'main':
                    case 'appetizer':
                        food[nameRaw] = (food[nameRaw] || 0) + qty;
                        break;
                    case 'drink':
                    case 'beverage':
                    case 'coffee':
                    case 'tea':
                        drink[nameRaw] = (drink[nameRaw] || 0) + qty;
                        break;
                    case 'dessert':
                    case 'sweet':
                    case 'cake':
                        dessert[nameRaw] = (dessert[nameRaw] || 0) + qty;
                        break;
                    case 'alcohol':
                    case 'wine':
                    case 'beer':
                    case 'cocktail':
                        alcohol[nameRaw] = (alcohol[nameRaw] || 0) + qty;
                        break;
                    default:
                        food[nameRaw] = (food[nameRaw] || 0) + qty;
                        break;
                }
            }
        });
    });

    // เรียงลำดับและเลือก top 10
    function top10(obj) {
        return Object.entries(obj)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10)
            .reduce((acc, [k, v]) => { acc[k] = v; return acc; }, {});
    }
    const filteredData = {
        food: top10(food),
        drink: top10(drink),
        dessert: top10(dessert),
        alcohol: top10(alcohol)
    };

    window.lastFilteredData = filteredData;

    updateMenuCards(filteredData);
    renderPopularChart(currentType, filteredData); // <<-- ส่ง filteredData ไปแสดงในกราฟ

    // อัปเดตหัวข้อกราฟ
    let period = 'เดือนนี้';
    if (startDate && endDate) {
        period = formatThaiDate(startDate) + ' ถึง ' + formatThaiDate(endDate);
    } else if (startDate) {
        period = 'ตั้งแต่ ' + formatThaiDate(startDate);
    } else if (endDate) {
        period = 'ถึง ' + formatThaiDate(endDate);
    }
    updateChartTitle(period, currentType);
}

// ฟังก์ชันแปลง yyyy-mm-dd เป็น วัน/เดือน/ปีไทย
function formatThaiDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d)) return '';
    const day = d.getDate();
    const month = d.getMonth() + 1;
    const year = d.getFullYear() + 543;
    const thaiMonths = [
        '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    return `${day} ${thaiMonths[month]} ${year}`;
}

// ฟังก์ชันอัปเดตการ์ดเมนู
function updateMenuCards(data) {
    const categories = ['food', 'drink', 'dessert', 'alcohol'];
    const titles = {
        food: { icon: '🍽️', name: 'อาหารยอดนิยม' },
        drink: { icon: '🥤', name: 'เครื่องดื่มยอดนิยม' },
        dessert: { icon: '🍰', name: 'ของหวานยอดนิยม' },
        alcohol: { icon: '🍷', name: 'เครื่องดื่มแอลกอฮอล์ยอดนิยม' }
    };
    
    categories.forEach(category => {
        const card = document.querySelector(`[data-category="${category}"]`);
        if (card) {
            const menuList = card.querySelector('.menu-list');
            const categoryData = data[category] || {};
            
            let html = '';
            let i = 1;
            for (const [menu, qty] of Object.entries(categoryData)) {
                if (i > 10) break;
                html += `
                    <div class="menu-item">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="menu-rank">${i}</div>
                            <span>${menu}</span>
                        </div>
                        <div class="menu-quantity">${qty}</div>
                    </div>
                `;
                i++;
            }
            
            if (html === '') {
                html = '<div class="no-data">ไม่มีข้อมูลในช่วงเวลานี้</div>';
            }
            
            menuList.innerHTML = html;
        }
    });
}

// ฟังก์ชันอัปเดตหัวข้อกราฟ
function updateChartTitle(period, type) {
    const title = document.getElementById('popularChartTitle');
    const typeNames = {
        food: 'อาหาร',
        drink: 'เครื่องดื่ม', 
        dessert: 'ของหวาน',
        alcohol: 'เครื่องดื่มแอลกอฮอล์'
    };
    
    if (title) {
        title.textContent = `📈 กราฟรวมเมนู${typeNames[type]}ยอดนิยม (${period})`;
    }
}

function onMenuCardClick(event, type) {
    event.stopPropagation();
    const dataSet = window.lastFilteredData || window.allTimeTopData;
    renderPopularChart(type, dataSet);

    // อัปเดตหัวข้อกราฟ
    let period = 'เดือนนี้';
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (startDate && endDate) {
        period = formatThaiDate(startDate) + ' ถึง ' + formatThaiDate(endDate);
    } else if (startDate) {
        period = 'ตั้งแต่ ' + formatThaiDate(startDate);
    } else if (endDate) {
        period = 'ถึง ' + formatThaiDate(endDate);
    }
    updateChartTitle(period, type);
}

// อัปเดตฟังก์ชัน renderPopularChart เดิม
window.renderPopularChart = function (type = 'food', customData = null) {
    const ctx = document.getElementById('popularChart');
    const title = document.getElementById('popularChartTitle');
    if (!ctx) return;

    // ป้องกัน error ถ้าไม่มี monthFilter
    const monthFilterElem = document.getElementById('monthFilter');
    const selectedMonth = monthFilterElem ? monthFilterElem.value : 'all';

    let datasets = customData;
    if (!datasets) {
        if (selectedMonth === 'all') {
            datasets = window.allTimeTopData;
        } else {
            datasets = window.monthlyTopData[selectedMonth] || {};
        }
    }

    const labelsMap = {
        food: 'อาหาร',
        drink: 'เครื่องดื่ม',
        dessert: 'ของหวาน',
        alcohol: 'เครื่องดื่มแอลกอฮอล์'
    };

    const colors = {
        food: '#3498db',
        drink: '#2ecc71',
        dessert: '#e67e22',
        alcohol: '#9b59b6'
    };

    const categoryData = datasets[type] || {};
    const labels = Object.keys(categoryData);
    const data = Object.values(categoryData);

    // เปลี่ยนหัวข้อกราฟ
    if (title && labelsMap[type]) {
        let period = 'เดือนนี้';
        if (monthFilterElem && selectedMonth !== 'all') {
            const date = selectedMonth.split('-');
            const thaiMonths = [
                '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const monthName = thaiMonths[parseInt(date[1])];
            const yearThai = parseInt(date[0]) + 543;
            period = `${monthName} ${yearThai}`;
        }
        title.textContent = `📈 กราฟรวมเมนู${labelsMap[type]}ยอดนิยม (${period})`;
    }

    if (charts.popularChart) {
        charts.popularChart.destroy();
    }

    charts.popularChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: `จำนวนออเดอร์ (${labelsMap[type]})`,
                data,
                backgroundColor: colors[type],
                borderRadius: 10,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
};
</script>

<!-- รายได้ -->
<section id="incomeSection" class="section-content hidden">
    <h3>💰 รายได้รวม</h3>
    <div class="chart-container">
        <h4>📈 กราฟรายได้รายเดือน</h4>
        <div class="chart-wrapper"><canvas id="incomeChart"></canvas></div>
    </div>

    <div class="table-container">
        <table>
            <thead><tr><th>ปี/เดือน/วัน</th><th>รายได้</th><th>การดำเนินการ</th></tr></thead>
            <tbody>
            <?php foreach ($incomeReport as $year => $yearData): ?>
                <tr class="year-row" onclick="toggleYear('<?= $year ?>')">
                    <td><strong><?= $year ?></strong></td>
                    <td><strong><?= number_format((float)($yearData['total'] ?? 0), 2) ?> ฿</strong></td>
                    <td><button class="toggle-btn">รายละเอียด</button></td>
                </tr>
                <?php foreach ($yearData['months'] as $month => $monthData): ?>
                    <tr class="month-row hidden" data-year="<?= $year ?>" data-month="<?= $month ?>" onclick="toggleMonth('<?= $year ?>','<?= $month ?>')">
                        <td style="padding-left:20px;">เดือน <?= $month ?></td>
                        <td><?= number_format((float)($monthData['total'] ?? 0), 2) ?> ฿</td>
                        <td><button class="toggle-btn">ดูรายวัน</button></td>
                    </tr>
                    <?php foreach ($monthData['days'] as $date => $dayTotal): ?>
                        <tr class="day-row hidden" data-year="<?= $year ?>" data-month="<?= $month ?>">
                            <td style="padding-left:40px;"><?= $date ?></td>
                            <td><?= number_format($dayTotal, 2) ?> ฿</td>
                            <td>-</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- เมนูและออเดอร์ -->
<section id="menuSection" class="section-content hidden">
    <h3>📋 รายงานเมนูและออเดอร์</h3>
    <div class="table-container">
        <table>
            <thead><tr><th>No.</th><th>เดือน</th><th>ยอดรวม</th><th>ออเดอร์</th><th>การดำเนินการ</th></tr></thead>
            <tbody>
                <?php $mIndex = 1; foreach ($monthlyOrders as $month => $data): ?>
                <tr>
                    <td><?= $mIndex++ ?></td>
                    <td><?= date('F Y', strtotime($month . '-01')) ?></td>
                    <td><?= number_format($data['total'], 2) ?> ฿</td>
                    <td><?= count($data['orders']) ?></td>
                    <td><button class="toggle-btn" onclick="toggleMonthDetails('month-<?= $month ?>')">ดูรายละเอียด</button></td>
                </tr>
                <tr id="month-<?= $month ?>" style="display: none;">
                    <td colspan="5" style="padding: 0;">
                        <div style="background: #f8f9fa; padding: 20px;">
                        <h4>รายละเอียดออเดอร์ เดือน <?= date('F Y', strtotime($month . '-01')) ?></h4>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th>ปี/เดือน/วัน</th>
                                    <th>ยอดรวม</th>
                                    <th>การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // จัดกลุ่มออเดอร์ตามวัน
                                $ordersByDay = [];
                                foreach ($data['orders'] as $order) {
                                    $date = date('Y-m-d', strtotime($order['created_at']));
                                    if (!isset($ordersByDay[$date])) {
                                        $ordersByDay[$date] = [
                                            'orders' => [],
                                            'total' => 0,
                                        ];
                                    }
                                    $ordersByDay[$date]['orders'][] = $order;
                                    $ordersByDay[$date]['total'] += $order['total'];

                                    if (!isset($ordersByDay[$date]['menuSummary'])) {
                                    $ordersByDay[$date]['menuSummary'] = [];
                                    }

                                    $lines = explode("\n", $order['order_details']);
                                    foreach ($lines as $line) {
                                        if (preg_match('/^(.*?)\s*(?:\((.*?)\))?\s*\*?(\d+)?\s*-\s*[\d.,]+\s*฿$/u', trim($line), $m)) {
                                            $menuName = trim($m[1]);
                                            $qty = isset($m[3]) && $m[3] ? (int)$m[3] : 1;
                                            $ordersByDay[$date]['menuSummary'][$menuName] =
                                                ($ordersByDay[$date]['menuSummary'][$menuName] ?? 0) + $qty;
                                        }
                                    }
                                }
                                $dIndex = 1;
                                foreach ($ordersByDay as $date => $dayData): ?>
                                    <?php $idKey = date('Ym', strtotime($month . '-01')) . date('d', strtotime($date)); ?>
                                    <tr class="order-day-row">
                                    <td><strong><?= $date ?></strong></td>
                                    <td><strong><?= number_format($dayData['total'], 2) ?> ฿</strong></td>
                                    <td><button class="toggle-btn" onclick="toggleOrderDayDetails('<?= $idKey ?>', event)">ดูออเดอร์</button></td>
                                    </tr>
                                    <tr id="order-day-<?= $idKey ?>" class="order-details-row"> <!--  style="display: none;" -->
                                        <td colspan="3" style="padding: 0;">
                                            <div style="background: #f8f9fa; padding: 15px;">
                                                <h4>📋 เมนูรวมประจำวันที่ <?= $date ?></h4>
                                                <table style="width: 100%; margin-top: 10px;">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>ชื่อเมนู</th>
                                                            <th>จำนวน</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $menuSummary = $ordersByDay[$date]['menuSummary'] ?? [];
                                                        $menuIndex = 1;
                                                        foreach ($menuSummary as $menu => $qty): ?>
                                                            <tr>
                                                                <td><?= $menuIndex++ ?></td>
                                                                <td><?= htmlspecialchars($menu) ?></td>
                                                                <td><?= $qty ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

</div> <!-- end .content-area -->
</div> <!-- end .main-container -->

<script>
window.reportData = {
    tableData: <?= json_encode($tableReservations); ?>,
    incomeData: <?= json_encode($incomeReport); ?>,
    topFoods: <?= json_encode($topFoods); ?>,
    topDrinks: <?= json_encode($topDrinks); ?>,
    topDesserts: <?= json_encode($topDesserts); ?>,
    topAlcohols: <?= json_encode($topAlcohols); ?>
};
window.allTimeTopData = {
    food: <?= json_encode($topFoods); ?>,
    drink: <?= json_encode($topDrinks); ?>,
    dessert: <?= json_encode($topDesserts); ?>,
    alcohol: <?= json_encode($topAlcohols); ?>
};
window.lastFilteredData = window.allTimeTopData;
</script>
<script src="js/report.js"></script>
</body>
</html>