<?php
session_start();
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $tour_id = $_POST['tour_id']; 
    $tour_name = $_POST['tenchuyendi'];
    $user_id = $_SESSION['UserID']; 
    $ho_va_ten = $_POST['HoVaTen'];
    $sdt = $_POST['SDT'];
    $email = $_POST['Email'];
    $dia_chi = htmlspecialchars(trim($_POST['DiaChi']));
    $so_luong = $_POST['so_luong'];

    // Xử lý biến price (Giá/người)
    $price = isset($_POST['gia_nguoi']) ? $_POST['gia_nguoi'] : '0';
    $price = str_replace(['.', ',', 'đồng'], '', $price); // Loại bỏ ký tự không hợp lệ
    $price = floatval($price); 

    // Xử lý biến money (Tổng số tiền)
    $money = isset($_POST['money']) ? $_POST['money'] : '0';
    $money = str_replace(['.', ',', 'đồng'], '', $money); // Loại bỏ ký tự không hợp lệ
    $money = floatval($money); 

    // Tránh làm tròn số quá nhiều nếu không cần thiết
    $price = round($price, 2);  
    $money = round($money, 2);  
    $thoi_gian_dat = $_POST['thoi_gian_dat'];
    $stmt = $conn->prepare("INSERT INTO booking_tour (tour_id, tour_name, UserID, HoVaTen, SDT, Email, DiaChi, so_luong, price, money, thoi_gian_dat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissssdiis", $tour_id, $tour_name, $user_id, $ho_va_ten, $sdt, $email, $dia_chi, $so_luong, $price, $money, $thoi_gian_dat);

   
    if ($stmt->execute()) {
        echo "<script>alert('Đặt tour thành công!'); window.location.href='my_tour.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

   
    $stmt->close();
    mysqli_close($conn);
}
