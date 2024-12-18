<?php
session_start();
include '../includes/connect.php';

// // Kiểm tra nếu người dùng đã đăng nhập
// if (!isset($_SESSION['UserID'])) {
//     header("Location: /projectphp/auth/login.php"); // Chuyển hướng đến trang đăng nhập
//     exit();
// }

// $tour_id = intval($_GET['tour_id']);
if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM Tours WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();
    } else {
        echo "Tour không tồn tại!";
        exit();
    }
} else {
    echo "Không tìm thấy ID của tour!";
    exit();
}

if (isset($_SESSION['UserID'])) {
    $user_id = $_SESSION['UserID'];
    $stmt_user = $conn->prepare("SELECT Name, Phone, Address FROM users WHERE UserID = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    }
} else {
    $user = null;
}

include('../layout/header.php');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Tour</title>
    <link rel="stylesheet" href="../template/css/detail_mb.css">
</head>

<body>
    <div class="content">
        <div id="trai">
            <div class="product-name">
                <h3 style="text-align: center;"><?= htmlspecialchars($tour['tour_name']) ?></h3>
            </div>
            <div class="product-img">
            <?php
                $image_path = '../tours/uploads/' . basename($tour['img']);
                ?>
                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($tour['tour_name']) ?>" title="Tour" style="width: 100%; height: auto; margin-left: 20px;">
            </div>
            <div class="product" style="height: auto;">
                <p style="font-size:16px;"><?= htmlspecialchars($tour['mota']) ?></p>
            </div>
            <div class="product">
                <h3 style="margin: 5px; color:#ed0080;">Lịch trình khởi hành: <?= htmlspecialchars($tour['thoigian']) ?></h3>
                <hr>
                <p style="font-size: 14px; color: #333;">
                    <?= str_replace('<img', '<img style="max-width: 100%; height: auto;"', $tour['description']) ?>
                </p>

            </div>
        </div>

        <div id="phai">
            <form action="xacNhan.php" id="form_xacnhan" method="GET" onsubmit="return checkLogin()">
                <div class="product">
                    <h4 style="text-align: center; color: rgb(255, 105, 231);"><?= htmlspecialchars($tour['tour_name']) ?></h4>
                    <input type="hidden" name="tour_name" value="<?= htmlspecialchars($tour['tour_name']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>ID Tour:</b></label>
                    <?= htmlspecialchars($tour['tour_id']) ?>
                    <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>Thời gian:</b></label>
                    <?= htmlspecialchars($tour['thoigian']) ?>
                    <input type="hidden" name="thoigian" value="<?= htmlspecialchars($tour['thoigian']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>Phương tiện:</b></label>
                    <?= htmlspecialchars($tour['transport']) ?>
                    <input type="hidden" name="transport" value="<?= htmlspecialchars($tour['transport']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>Khách sạn:</b></label>
                    <?= htmlspecialchars($tour['khachsan']) ?>
                    <input type="hidden" name="khachsan" value="<?= htmlspecialchars($tour['khachsan']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>Xuất phát:</b></label>
                    <?= htmlspecialchars($tour['departure_location']) ?>
                    <input type="hidden" name="departure_location" value="<?= htmlspecialchars($tour['departure_location']) ?>">
                    <hr>
                </div>

                <div class="product">
                    <label for=""><b>Giá/người:</b></label>
                    <?= str_replace('.', ' ', number_format($tour['price'], 0, ",", ".")) ?> đ
                    <input style="color: rgb(255, 139, 164);" type="hidden" name="price" value="<?= htmlspecialchars($tour['price']) ?>">
                    <hr>
                </div>


                <div>
                    <button type="submit" id="button123">Đặt Tour</button>
                </div>
            </form>

            <?php if (isset($user) && $user): ?>
                <!-- Form gửi nhận xét -->
                <form action="../feedback/process_feedback.php" id="form_feedback" method="POST" onsubmit=" return checkLogin1()">
                    <h4 style="margin-bottom: 5px; color: #ed0080;">Nếu có nhận xét điều gì về tour hãy góp ý với chúng tôi?</h4>
                    <div class="feed_back">
                        <textarea name="feedback" id="feedback" placeholder="Vui lòng nhập nhận xét của bạn vào đây..." required></textarea>
                    </div>
                    <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">
                    <input type="hidden" name="UserID" value="<?= isset($_SESSION['UserID']) ? htmlspecialchars($_SESSION['UserID']) : '' ?>">
                    <div class="confirm">
                        <label for="rating">Đánh giá:</label>
                        <select name="rating" id="rating" required>
                            <option value="1">1 - Rất tệ</option>
                            <option value="2">2 - Tệ</option>
                            <option value="3">3 - Trung bình</option>
                            <option value="4">4 - Tốt</option>
                            <option value="5">5 - Rất tốt</option>
                        </select>
                    </div>
                    <div class="confirm">
                        <button type="submit" id="button12345">Gửi</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Vui lòng đăng nhập để gửi nhận xét!</p>
            <?php endif; ?>

            <script>
                function checkLogin() {
                    <?php if (!isset($_SESSION['UserID'])): ?>
                        var userChoice = confirm("Bạn chưa đăng nhập để đặt Tours. Vui lòng đăng nhập!");
                        if (userChoice) {
                            window.location.href = "/projectphp/auth/login.php";
                        }
                        return false;
                    <?php else: ?>
                        return true;
                    <?php endif; ?>
                }

                function checkLogin1() {
                    <?php if (!isset($_SESSION['UserID'])): ?>
                        var userChoice = confirm("Bạn chưa đăng nhập để nhận xét !. Vui lòng đăng nhập!");
                        if (userChoice) {
                            window.location.href = "/projectphp/auth/login.php";
                        }
                        return false;
                    <?php else: ?>
                        return true;
                    <?php endif; ?>
                }
            </script>
        </div>
    </div>
</body>

</html>

<?php include('../layout/footer.php') ?>