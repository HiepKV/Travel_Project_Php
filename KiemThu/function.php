<?php
include '../includes/connect.php';
session_start();

function checkTaiKhoanTonTai($conn, $taiKhoan) {
    $result = $conn->query("SELECT TaiKhoan FROM nguoidung WHERE TaiKhoan = '$taiKhoan'");
    return $result->num_rows > 0;
}
function validatePassword($matKhau, $xacnhanmatkhau) {
    if ($matKhau != $xacnhanmatkhau) {
        return "Mật khẩu và xác nhận mật khẩu không khớp.";
    } 
    if (strlen($matKhau) < 6) {
        return "Độ dài mật khẩu phải lớn hơn 6 ký tự.";
    }
    $chuHoa = preg_match('@[A-Z]@', $matKhau);
    $chuThuong = preg_match('@[a-z]@', $matKhau);
    $So = preg_match('@[0-9]@', $matKhau);
    if (!$chuHoa || !$chuThuong || !$So) {
        return "Yêu cầu mật khẩu của bạn phải có cả chữ in hoa, chữ thường và số!";
    }
    return ''; // Không có lỗi
}
function validatePhoneNumber($sdt) {
    // Kiểm tra xem số điện thoại có chỉ chứa chữ số và có độ dài 10 ký tự hay không
    if (!preg_match('/^[0-9]{10}$/', $sdt)) {
        return "Định dạng số điện thoại của bạn chưa đúng.";
    }
    return ''; // Không có lỗi
}

function registerUser($conn, $taiKhoan, $matKhau, $hovaten, $sdt, $email, $diachi, $role) {
    $stmt = $conn->prepare("INSERT INTO nguoidung (TaiKhoan, MatKhau, HoVaTen, SDT, Email, DiaChi, Role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $taiKhoan, $matKhau, $hovaten, $sdt, $email, $diachi, $role);
    return $stmt->execute();
}


?>