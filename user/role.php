<?php
include '../includes/connect.php';

// Check if user ID is provided
if (!isset($_GET['id'])) {
    echo "Không có ID người dùng.";
    exit;
}

$UserID = $_GET['id'];

$sql = "SELECT Role FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


if (!$user) {
    echo "Không tìm thấy người dùng.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Role = $_POST['Role'];

    $update_sql = "UPDATE Users SET Role = ? WHERE UserID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $Role, $UserID);

    if ($update_stmt->execute()) {
        echo "<script>
                alert('Cập nhật vai trò thành công!');
                window.location.href = '../admin/dashboard.php?page=user'; // Chuyển hướng đến trang user
              </script>";
        exit;
    } else {
        $error = "Cập nhật vai trò thất bại.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân quyền người dùng</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../template/css/user.css">
</head>
<body>
    <div class="form-container">
        <h1 class="page-header">Phân quyền người dùng</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Vai trò:</label>
                <select name="Role">
                    <option value="Customer" <?= $user['Role'] == 'Customer' ? 'selected' : '' ?>>Customer</option>
                    <option value="Admin" <?= $user['Role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="button-container">
                <button type="submit" class="btn-success">Cập nhật</button>
            </div>
        </form>
    </div>
</body>

</html>