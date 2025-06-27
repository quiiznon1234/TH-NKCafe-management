<?php
session_start(); // เริ่ม session

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
$isLoggedIn = isset($_SESSION['username']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user'; // ค่าเริ่มต้นเป็น user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think Cafe - Studios</title>
    <link rel="stylesheet" href="css/studio.css">
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
                <li><a href="<?php echo ($role === 'admin') ? 'Admin_menu.php' : 'user_menu.php'; ?>">menu</a></li>
                <li><a href="event.php" class="active">Event</a></li>
             <li class="dropdown">
                <a href="#" class="dropbtn">More ▼</a>
                <div class="dropdown-content">
                    <a href="wedding.php">Wedding</a>
                    <a href="studios.php" class="active">Studios</a>
                    <a href="meeting.php">Meeting</a>
                    <a href="Pmeeting.php">Private-meeting</a>
                </div>
              </li>
        </nav>
    </header>
    
    <main class="animate-up delay-2">
        <h1 class="animate-up delay-2">The Bloc Photo Studios</h1>
        <div class="studio-slider animate-up delay-3">
            <div class="studio-container">
                <!-- STUDIO 1 -->
                <div class="studio-slide active">
                    <h3>Studio 1</h3>
                    <div class="image-wrapper">
                    <img src="img/studio.jpg" alt="Studio 1">
                    </div>
                    <p>ขนาดลึกถึง 12 เมตร กว้าง 2.4 เมตร... สไตล์มินิมอล โทนขาว</p>
                    <div class="divider"></div>
                    <p><strong>ค่าเช่าสตูดิโอ + สวน</strong><br>
                        ครึ่งวัน 2000 บาท<br>เต็มวัน 3500 บาท</p>
                    <div class="divider"></div>
                    <p><strong>ค่าเช่าสตูดิโอ + คาเฟ่ + สวน</strong><br>
                        ครึ่งวัน 4000 บาท<br>เต็มวัน 5500 บาท</p>
                    <div class="divider"></div>
                    <p><strong>เวลาบริการ</strong><br>
                        ครึ่งวันเช้า 9.30 - 13.30 น.<br>
                        ครึ่งวันบ่าย 14.00 - 18.00 น.<br>
                        เต็มวัน 9.30 - 17.30 น.</p>
                    <p><strong>RESERVATION:</strong> 085-370-6367, 086-555-8789</p>
                    <button class="reserve-btn" onclick="window.location.href='reserveE.php?event=Shooting Studios'">จองวัน (Studios)</button>
                </div>

                <!-- STUDIO 2 -->
                <div class="studio-slide">
                    <h3>Studio 2</h3>
                    <div class="image-wrapper">
                        <img src="img/studio3.jpg" alt="Studio 2">
                    </div>
                    <p>ขนาดลึกถึง 12 เมตร กว้าง 7 เมตร มีหน้าต่างขนาดใหญ่ รับแสงธรรมชาติโดยตรง สไตล์ห้องโล่ง โทนขาว เหมาะสำหรับถ่ายโฆษณาหรือเซตถ่ายขนาดใหญ่</p>
                    <div class="divider"></div>
                    <p><strong>ค่าเช่าสตูดิโอ</strong><br>
                        ครึ่งวัน 4500 บาท<br>เต็มวัน 7000 บาท</p>
                    <div class="divider"></div>
                    <p><strong>ค่าเช่าสตูดิโอ + คาเฟ่ + สวน</strong><br>
                        ครึ่งวัน 6000 บาท<br>เต็มวัน 8500 บาท</p>
                    <div class="divider"></div>
                    <p><strong>เวลาบริการ</strong><br>
                        ครึ่งวันเช้า 9.30 - 13.30 น.<br>
                        ครึ่งวันบ่าย 14.00 - 18.00 น.<br>
                        เต็มวัน 9.30 - 17.30 น.</p>
                    <p><strong>RESERVATION:</strong> 085-370-6367, 086-555-8789</p>
                    <button class="reserve-btn" onclick="window.location.href='reserveE.php?event=Shooting Studios'">จองวัน (Studios)</button>
                </div>

                <!-- STUDIO 3 -->
                <div class="studio-slide">
                    <h3>Studio 3</h3>
                    <div class="image-wrapper">
                        <img src="img/simpledays1.jpg" alt="Studio 3">
                    </div>
                    <p>สวนขนาดใหญ่ และคาเฟ่สไตล์ญี่ปุ่น ที่สามารถถ่ายได้ทุกมุมทั้งภายในและภายนอก</p>
                    <div class="divider"></div>
                    <p><strong>ค่าเช่าซิมเปิ้ลเดย์คาเฟ่ + สวน</strong><br>
                        รายชั่วโมง 1000 บาท<br>
                        ครึ่งวัน 3000 บาท<br>
                        เต็มวัน 5000 บาท</p>
                    <div class="divider"></div>
                    <p><strong>เวลาบริการ</strong><br>
                        ครึ่งวันเช้า 9.30 - 13.30 น.<br>
                        ครึ่งวันบ่าย 14.00 - 18.00 น.<br>
                        เต็มวัน 9.30 - 17.30 น.</p>
                    <p><strong>RESERVATION:</strong> 085-370-6367, 086-555-8789</p>
                    <button class="reserve-btn" onclick="window.location.href='reserveE.php?event=Shooting Studios'">จองวัน (Studios)</button>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button class="arrow left" onclick="prevStudio()">&#10094;</button>
            <button class="arrow right" onclick="nextStudio()">&#10095;</button>
        </div>

        <!-- Slide Indicators -->
        <div class="slide-indicators animate-up delay-4">
            <div class="indicator active" onclick="goToSlide(0)"></div>
            <div class="indicator" onclick="goToSlide(1)"></div>
            <div class="indicator" onclick="goToSlide(2)"></div>
        </div>
    </main>
<script src="js/studio.js"></script>
</body>
</html>