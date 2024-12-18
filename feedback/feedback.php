<?php
session_start();
include '../includes/connect.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['UserID'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['UserID']; // Lấy UserID từ session

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

$sql = "SELECT fb.id_fb, fb.tour_id, fb.rating, fb.feedback, t.tour_name 
        FROM feed_back fb
        JOIN Tours t ON fb.tour_id = t.tour_id
        WHERE fb.UserID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu người dùng đã gửi đánh giá cho tour
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
// đánh giá cho tour đã được được xác nhận 
$sql = "SELECT bt.id_booking_tour, t.tour_name 
        FROM booking_tour bt
        JOIN Tours t ON bt.tour_id = t.tour_id
        WHERE bt.UserID = ? 
        AND bt.status = 'Đã xác nhận' 
        AND bt.id_booking_tour NOT IN (SELECT tour_id FROM feed_back WHERE UserID = ?)";
// Lấy danh sách các đánh giá của người dùng từ bảng feed_back
$sql = "SELECT fb.id_fb, bt.id_booking_tour, t.tour_name, fb.rating, fb.feedback 
        FROM feed_back fb
        JOIN booking_tour bt ON fb.tour_id = bt.tour_id
        JOIN Tours t ON bt.tour_id = t.tour_id
        WHERE bt.UserID = ? AND bt.status = 'Đã xác nhận'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Cập nhật đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_feedback_id'])) {
    $feedback_id = $_POST['update_feedback_id'];
    $updated_rating = $_POST['rating'];
    $updated_feedback = $_POST['feedback'];

    // Cập nhật đánh giá trong cơ sở dữ liệu
    $update_sql = "UPDATE feed_back SET rating = ?, feedback = ? WHERE id_fb = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("isi", $updated_rating, $updated_feedback, $feedback_id);

    if ($stmt->execute()) {
        echo "<script>alert('Đánh giá đã được cập nhật!'); window.location.href = 'feedback.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}

// Gửi đánh giá mới cho tour chưa có đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_booking_id'])) {
    $new_booking_id = $_POST['new_booking_id'];
    $new_rating = $_POST['new_rating'];
    $new_feedback = $_POST['new_feedback'];

    // Lấy tour_id từ bảng booking_tour
    $get_tour_sql = "SELECT tour_id FROM booking_tour WHERE id_booking_tour = ? AND UserID = ?";
    $stmt = $conn->prepare($get_tour_sql);
    $stmt->bind_param("ii", $new_booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>alert('Tour không tồn tại!');</script>";
        exit;
    }

    $tour_row = $result->fetch_assoc();
    $tour_id = $tour_row['tour_id'];

    // Kiểm tra nếu tour đã được xác nhận
    $check_sql = "SELECT * FROM booking_tour bt
                  WHERE bt.id_booking_tour = ? AND bt.UserID = ? AND bt.status = 'Đã xác nhận'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $new_booking_id, $user_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows == 0) {
        echo "<script>alert('Tour này chưa được xác nhận hoặc bạn chưa đặt tour này!');</script>";
    } else {
        // Kiểm tra nếu người dùng đã đánh giá tour này
        $check_sql = "SELECT * FROM feed_back WHERE tour_id = ? AND UserID = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $tour_id, $user_id);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Bạn đã đánh giá tour này rồi!');</script>";
        } else {
            $insert_sql = "INSERT INTO feed_back (UserID, tour_id, rating, feedback) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iiis", $user_id, $tour_id, $new_rating, $new_feedback);

            if ($stmt->execute()) {
                echo "<script>alert('Đánh giá đã được gửi!'); window.location.href = 'feedback.php';</script>";
            } else {
                echo "Lỗi: " . $stmt->error;
            }
        }
    }
}
?>

<?php include('../layout/header.php'); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Của Tôi</title>
    <link rel="stylesheet" href="..//template/css/feedback.css">
</head>

<body>
    <div class="feedback-page">
        <div class="feedback-container">
            <h2 class="feedback-title">Danh Sách Đánh Giá Của Tôi</h2>

            <?php if (!empty($reviews)): ?>
                <h3>Đánh Giá Đã Gửi</h3>
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>Tên Tour</th>
                            <th>Đánh Giá</th>
                            <th>Nhận Xét</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td> <a href="../booking/detail_mb.php?id=<?= (int)$review['tour_id'] ?>" class="tour-link">
                                        <?= htmlspecialchars($review['tour_name']); ?>
                                    </a></td>
                                <td><?php echo str_repeat('⭐', $review['rating']); ?></td>
                                <td><?php echo htmlspecialchars($review['feedback']); ?></td>
                                <td>
                                    <!-- Form to update feedback -->
                                    <form method="POST" class="feedback-update-form">
                                        <input type="hidden" name="update_feedback_id" value="<?php echo $review['id_fb']; ?>">
                                        <label for="rating_<?php echo $review['id_fb']; ?>">Cập nhật đánh giá:</label>
                                        <select name="rating" id="rating_<?php echo $review['id_fb']; ?>" required>
                                            <option value="1" <?php echo ($review['rating'] == 1) ? 'selected' : ''; ?>>1 sao</option>
                                            <option value="2" <?php echo ($review['rating'] == 2) ? 'selected' : ''; ?>>2 sao</option>
                                            <option value="3" <?php echo ($review['rating'] == 3) ? 'selected' : ''; ?>>3 sao</option>
                                            <option value="4" <?php echo ($review['rating'] == 4) ? 'selected' : ''; ?>>4 sao</option>
                                            <option value="5" <?php echo ($review['rating'] == 5) ? 'selected' : ''; ?>>5 sao</option>
                                        </select>
                                        <textarea name="feedback" required><?php echo htmlspecialchars($review['feedback']); ?></textarea>
                                        <button type="submit">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Bạn chưa có đánh giá nào.</p>
            <?php endif; ?>

            <h3>Đánh Giá Các Tour Đã Đặt</h3>
            <form method="POST" class="feedback-new-form">
                <label for="new_booking_id">Chọn Tour để Đánh Giá:</label>
                <select name="new_booking_id" id="new_booking_id" required>
                    <option value="">Chọn Tour</option>
                    <?php
                    // Lấy danh sách các tour đã đặt nhưng chưa đánh giá và có trang thái là đã xác nhận
                    $sql = "SELECT bt.id_booking_tour, t.tour_name 
FROM booking_tour bt
JOIN Tours t ON bt.tour_id = t.tour_id
WHERE bt.UserID = ? 
AND bt.status = 'Đã xác nhận'
AND bt.tour_id NOT IN (SELECT tour_id FROM feed_back WHERE UserID = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $user_id, $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id_booking_tour'] . "'>" . htmlspecialchars($row['tour_name']) . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="new_rating">Đánh giá:</label>
                <select name="new_rating" id="new_rating" required>
                    <option value="1">1 sao</option>
                    <option value="2">2 sao</option>
                    <option value="3">3 sao</option>
                    <option value="4">4 sao</option>
                    <option value="5">5 sao</option>
                </select><br><br>

                <label for="new_feedback">Nhận xét:</label><br>
                <textarea name="new_feedback" id="new_feedback" rows="4" cols="50" required></textarea><br><br>
                <button type="submit">Gửi Đánh Giá</button>
            </form>

        </div>
    </div>
</body>

</html>