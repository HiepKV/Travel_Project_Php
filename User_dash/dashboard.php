<?php
session_start();
include('../includes/connect.php');
if (isset($_SESSION['UserID'])) {
    $user_id = $_SESSION['UserID'];
}
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



$query = "SELECT users.Name, users.Phone, users.Address, account.Email 
          FROM users 
          JOIN account ON users.UserID = account.UserID 
          WHERE users.UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

if ($result = $stmt->get_result()) {
    $user = $result->fetch_assoc();
} else {
    echo "Lỗi khi truy vấn thông tin người dùng: " . $stmt->error;
    exit();
}
?>
<?php
include('../layout/header.php')
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Người dùng</title>
    <!-- <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css"> -->
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="../slider/slider.css">
    <link rel="stylesheet" href="../template/css/user_db.css">
    <script src="../slider/slider.js"></script>
</head>

<body>
    <!-- Slider -->
    <div class="slider">
        <div class="slides">
            <div class="slide"><img src="/projectphp/img/indeximg/slide1.jpg" alt="Slide 1"></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide2.jpg" alt="Slide 2"></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide3.jpg" alt="Slide 3"></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide4.jpg" alt="Slide 4"></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide5.jpg" alt=""></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide6.jpg" alt=""></div>
            <div class="slide"><img src="/projectphp/img/indeximg/slide7.jpg" alt=""></div>
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
        slidesPerView: 2, // Hiển thị 5 slide cùng lúc
        spaceBetween: 20, // Khoảng cách giữa các slide
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
            1440: {
                slidesPerView: 4,
            }
        },
        on: {
            init: function() {
                // Kiểm tra số lượng slide và điều chỉnh nếu cần
                if (this.slides.length < 5) {
                    this.params.slidesPerView = this.slides.length; // Điều chỉnh số slide hiển thị theo số lượng slide có
                    this.update(); // Cập nhật swiper
                }
            }
        }
    });
</script>

    <!-- Footer -->
</body>

</html>
<?php
include('../layout/footer.php')
?>