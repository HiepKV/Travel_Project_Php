<?php 
include("../includes/connect.php");
session_start(); // Start the session if it's not already started
if (isset($_SESSION['UserID'])) {
    $user_id = $_SESSION['UserID'];
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
if (!isset($user)) {
    $user = ['Email' => null];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../template/css/admin.css">
    <title>Document</title>
</head>
<body class="admin-dashboard-body">
<header class="admin-header">
    <h1 class="admin-title">Admin Dashboard</h1>
    <p class="admin-welcome-message">Chào mừng, <span class="admin-name"><?php echo htmlspecialchars($user['Email'] ?? 'No Email Provided'); ?></span></p>
    <nav class="admin-nav">
        <ul class="admin-nav-list">
            <li><a class="admin-nav-item" href="http://localhost/projectphp/admin/dashboard.php">Tổng quan</a></li>
            <li><a class="admin-nav-item" href="http://localhost/projectphp/tours/tour.php">Bảo Trì Điểm Đến</a></li>
            <li><a class="admin-nav-item" href="http://localhost/projectphp/admin/booking.php">Thông Tin Đặt Tour</a></li>
            <li><a class="admin-nav-item" href="http://localhost/projectphp/user/list.php">Quản Lý Tài Khoản Người Dùng</a></li>
            <li><a class="admin-nav-item" href="http://localhost/projectphp/admin/feedback.php?tour=&rating=">Xem Đánh Giá Người Dùng</a></li>
            <li><a class="admin-nav-item" href="../auth/logout.php">Đăng Xuất</a></li>
        </ul>
    </nav>
</header>

</body>
</html>
