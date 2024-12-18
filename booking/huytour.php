<?php
session_start();
include '../includes/connect.php';
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['UserID'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$user_id = $_SESSION['UserID'];
$booking_id = $_POST['booking_id'] ?? null;

// Kiểm tra tính hợp lệ của booking_id
if (!$booking_id || !is_numeric($booking_id)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu hoặc sai thông tin tour']);
    exit();
}

// Kiểm tra chi tiết tour trước khi hủy
$check_query = "SELECT status, thoi_gian_dat FROM booking_tour WHERE id_booking_tour = ? AND UserID = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// Kiểm tra tồn tại tour
if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy tour']);
    exit();
}

// Kiểm tra trạng thái và thời gian đặt tour
$booking_time = strtotime($booking['thoi_gian_dat']);
$current_time = time();
$hours_since_booking = ($current_time - $booking_time) / 3600;

// Ngăn chặn hủy tour đã xác nhận
if (strtolower($booking['status']) == 'đã xác nhận') {
    echo json_encode([ 
        'success' => false, 
        'message' => 'Không thể hủy tour đã được xác nhận'
    ]);
    exit();
}

// Ngăn chặn hủy tour đã hủy hoặc quá 24 giờ
if (strtolower($booking['status']) == 'đã hủy') {
    echo json_encode([ 
        'success' => false, 
        'message' => 'Tour này đã bị hủy trước đó'
    ]);
    exit();
}

// Ngăn chặn hủy tour quá 24 giờ
if ($hours_since_booking > 24) {
    echo json_encode([ 
        'success' => false, 
        'message' => 'Đã quá thời gian hủy tour (24 giờ)'
    ]);
    exit();
}

// Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
$conn->begin_transaction();

try {
    // Cập nhật trạng thái tour thành "Đã hủy"
    $update_query = "UPDATE booking_tour SET status = 'Đã hủy' WHERE id_booking_tour = ? AND UserID = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $booking_id, $user_id);

    if (!$update_stmt->execute()) {
        throw new Exception('Lỗi cập nhật trạng thái tour');
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Hủy tour thành công']);
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

// Đóng các kết nối
$stmt->close();
$update_stmt->close();
$conn->close();
?>
