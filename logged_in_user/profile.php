<?php
session_start();
include('../includes/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['UserID'])) {
    header("Location: /projectphp/auth/login.php");
    exit();
}

$user_id = $_SESSION['UserID'];

// Truy vấn thông tin người dùng
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

$message = ""; // Thông báo cho người dùng

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    // Cập nhật bảng users
    $update_query_users = "UPDATE users SET Name = ?, Phone = ?, Address = ? WHERE UserID = ?";
    $update_stmt_users = $conn->prepare($update_query_users);
    $update_stmt_users->bind_param("sssi", $name, $phone, $address, $user_id);

    $update_users_success = $update_stmt_users->execute();

    // Cập nhật bảng account
    $update_query_account = "UPDATE account SET Email = ? WHERE UserID = ?";
    $update_stmt_account = $conn->prepare($update_query_account);
    $update_stmt_account->bind_param("si", $email, $user_id);

    $update_account_success = $update_stmt_account->execute();

    // Kiểm tra kết quả và thiết lập thông báo
    if ($update_users_success && $update_account_success) {
        $message = "Thông tin đã được cập nhật thành công!";
        header("Location: profile.php?success=1");
        exit();
    } else {
        $message = "Đã xảy ra lỗi khi cập nhật thông tin.";
    }
}

?>
<?php
        include('../layout/header.php');
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="../template/css/profile.css"> 
    
</head>
<body>
    <div class="container">
        <h2>Thông tin cá nhân</h2>
        <?php if (!empty($message)) : ?>
        <div class="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
        <form method="POST">
        <form method="POST">
        <label for="name">Họ và tên:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name'] ?? ''); ?>" required>

        <label for="phone">Số điện thoại:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">

        <label for="address">Địa chỉ:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>">

        <label for="email">Email của bạn</label>
        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>">

    <button type="submit">Cập nhật thông tin</button>
</form>

        </form>
    </div>
</body>
</html>
<?php
        include('../layout/footer.php');
?>

