<?php
session_start();
include '../includes/connect.php';

// Kiểm tra nếu người dùng đã gửi phản hồi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra xem các giá trị cần thiết có tồn tại trong POST hay không
    if (isset($_POST['feedback'], $_POST['tour_id'], $_POST['UserID'], $_POST['rating'])) {
        // Lấy dữ liệu từ form và làm sạch
        $feedback = htmlspecialchars(trim($_POST['feedback']));
        $tour_id = (int)$_POST['tour_id'];
        $user_id = (int)$_POST['UserID'];
        $rating = (int)$_POST['rating'];
        $stmt_user_check = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
        $stmt_user_check->bind_param("i", $user_id);
        $stmt_user_check->execute();
        $user_check_result = $stmt_user_check->get_result();

        if ($user_check_result->num_rows == 0) {
            echo "Người dùng không tồn tại!";
            exit();
        }
        // Kiểm tra kết nối cơ sở dữ liệu
        if (!$conn) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }

        // Xác thực dữ liệu đầu vào
        if (empty($feedback)) {
            echo "<script>alert('Vui lòng nhập nhận xét của bạn.'); window.history.back();</script>";
            exit();
        }

        if ($rating < 1 || $rating > 5) {
            echo "<script>alert('Vui lòng chọn một đánh giá hợp lệ.'); window.history.back();</script>";
            exit();
        }

        // Chuẩn bị truy vấn
        $stmt = $conn->prepare("INSERT INTO feed_back (tour_id, UserID, feedback, rating) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Lỗi trong việc chuẩn bị truy vấn: " . $conn->error);
        }

        // Liên kết tham số vào truy vấn
        $stmt->bind_param("iisi", $tour_id, $user_id, $feedback, $rating);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            echo "<script>alert('Phản hồi của bạn đã được gửi. Cảm ơn bạn!'); window.location.href = 'feedback.php';</script>";
            exit();
        } else {
            echo "Có lỗi xảy ra khi lưu dữ liệu: " . $stmt->error;
        }

        // Đóng truy vấn
        $stmt->close();
    } else {
        echo "<script>alert('Dữ liệu không hợp lệ. Vui lòng thử lại.'); window.history.back();</script>";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
