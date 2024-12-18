<?php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
include 'C:\xampp\htdocs\projectphp\includes\connect.php';
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn để lấy các tour được đánh giá cao nhất
$sql = "SELECT 
                t.tour_id, 
                t.tour_name, 
                t.img, 
                t.price,
                AVG(f.rating) AS avg_rating,
                COUNT(f.rating) AS rating_count
            FROM 
                Tours t
            JOIN 
                feed_back f ON t.tour_id = f.tour_id
            GROUP BY 
                t.tour_id, t.tour_name, t.img, t.price
            HAVING 
                avg_rating >= 4.5
            ORDER BY 
                avg_rating DESC, rating_count DESC
            LIMIT 6";
$result = $conn->query($sql);

$tours = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tours[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <!-- <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css"> -->

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="slider/slider.css">
    <link rel="stylesheet" href="style.css">
    <style>
        #webName {
            color: pink;
            /* Màu hồng ban đầu */
            transition: color 1s ease;
            /* Hiệu ứng chuyển màu mượt mà */
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    let webName = document.getElementById("webName");
    setInterval(function() {
        // Lấy màu sắc hiện tại của phần tử từ CSS
        let currentColor = window.getComputedStyle(webName).color;
        
        // Kiểm tra màu hiện tại và thay đổi
        if (currentColor === "rgb(255, 192, 203)") {  // Màu hồng (pink)
            webName.style.color = "white";  // Màu white
        } else {
            webName.style.color = "pink";  // Màu hồng
        }
    }, 2000);  // Chuyển đổi mỗi 2 giây
});
    </script>
</head>
<?
?>
<!-- php include('./layout/header.php') -->

<body>
    <header>
        <div id="banner">
            <div class="logo-container">
                <div class="logo">
                    <img id="logo" src="./img/indeximg/banner.png">
                </div>
                <div class="web_name">
                    <h2 id="webName">TravelTrip</h2>
                    <p><i class="fa-solid fa-plane"></i> Bạn muốn đi đâu?</p>
                </div>
            </div>
            <div class="menu">
                <ul>
                    <li style="width: 100px;"><a href="index.php"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
                    <li><a href=""><i class="fa-solid fa-layer-group"></i> Đánh giá</a></li>
                    <li><a href=""><i class="fa-solid fa-newspaper"></i> Tour</a></li>
                    <li class="taikhoan"><a id="accountLink"><i class="fa-solid fa-user"></i> Tài Khoản</a></li>
                </ul>
            </div>

        </div>
        <div class="form_Account">
            <button type="button" style="background-color: rgb(0, 228, 255);" onclick="window.location.href='http://localhost/projectphp/auth/login.php'">Bạn đã có tài khoản</button>
            <p>Bạn chưa có tài khoản? <a href="http://localhost/projectphp/auth/register.php">Đăng ký</a> ngay</p>
        </div>
        <div class="bamien">
            <div id="mb"><a href="http://localhost/projectphp/logged_in_user/mienbac.php">MIỀN BẮC</a></div>
            <div id="mt"><a href="http://localhost/projectphp/logged_in_user/mientrung.php">MIỀN TRUNG</a></div>
            <div id="mn"><a href="http://localhost/projectphp/logged_in_user/miennam.php">MIỀN NAM</a></div>
        </div>
    </header>

    <!-- Slider -->
    <div class="slider">
        <div class="slides">
            <div class="slide"><img src="./img/./indeximg/./slide1.jpg" alt="Slide 1"></div>
            <div class="slide"><img src="./img/./indeximg/./slide2.jpg" alt="Slide 2"></div>
            <div class="slide"><img src="./img/./indeximg/./slide3.jpg" alt="Slide 3"></div>
            <div class="slide"><img src="./img/./indeximg/./slide4.jpg" alt="Slide 4"></div>
            <div class="slide"><img src="./img/./indeximg/./slide5.jpg" alt=""></div>
            <div class="slide"><img src="./img/./indeximg/./slide6.jpg" alt=""></div>
            <div class="slide"><img src="./img/./indeximg/./slide7.jpg" alt=""></div>
        </div>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <!-- Các địa điểm nổi bật -->
    <div class="contain">
        <div class="heading">
            <h2>CÁC TOUR NỔI BẬT ĐƯỢC ĐÁNH GIÁ CAO</h2>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                        <div class="swiper-slide">
                            <div class="box">
                                <div class="imgBox">
                                    <img src="/projectphp/tours/uploads/<?= htmlspecialchars(str_replace('uploads/', '', $tour['img'])) ?>"
                                        alt="Hình ảnh tour" title="ảnh" style="width: auto; height: 270px;">
                                </div>
                                <div class="name-text left1">
                                    <h3><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                                    <div class="tour-details">
                                        <p>Đánh giá: <?php echo number_format($tour['avg_rating'], 1); ?>/5
                                            (<?php echo $tour['rating_count']; ?> đánh giá)</p>
                                        <p>Giá: <?php echo number_format($tour['price'], 0, ',', '.'); ?> VNĐ</p>
                                    </div>
                                    <a href="../booking/detail_mb.php?id=<?= (int)$tour['tour_id'] ?>" class="btn">Xem Chi Tiết</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Hiện tại chưa có tour nổi bật.</p>
                <?php endif; ?>
            </div>
            <!-- Add navigation buttons -->
<div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <script>
        const swiper = new Swiper('.swiper', {
            slidesPerView: 4, // Hiển thị 5 slide cùng lúc
            spaceBetween: 20, // Khoảng cách giữa các slide
            navigation: {
                nextEl: '.swiper-button-next', // Nút chuyển sang phải
                prevEl: '.swiper-button-prev', // Nút chuyển sang trái
            },
            loop: true, // Lặp lại slider
            breakpoints: {
                768: {
                    slidesPerView: 2, // Hiển thị 2 slide trên màn hình >= 768px
                },
                1024: {
                    slidesPerView: 3, // Hiển thị 4 slide trên màn hình >= 1024px
                },
                1440: {
                    slidesPerView: 4, // Hiển thị 5 slide trên màn hình >= 1440px
                }
            },
        });
    </script>


    <script src="slider/slider.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const accountLink = document.getElementById('accountLink');
            const formAccount = document.querySelector('.form_Account');
            formAccount.style.display = 'none';
            accountLink.addEventListener('click', (event) => {
                event.preventDefault();
                if (formAccount.style.display === 'none') {
                    formAccount.style.display = 'block';
                } else {
                    formAccount.style.display = 'none';
                }
            });

            document.addEventListener('click', (event) => {
                if (!accountLink.contains(event.target) && !formAccount.contains(event.target)) {
                    formAccount.style.display = 'none';
                }
            });
            formAccount.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });
    </script>

    
</body>

</html>
<?php
include('./layout/footer.php')
?>