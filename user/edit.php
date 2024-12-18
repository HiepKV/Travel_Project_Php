<?php
include '../includes/connect.php';

$sql = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Name = $_POST['Name'];
    $Phone = $_POST['Phone'];
    $Address = $_POST['Address'];

    $update_sql = "UPDATE Users SET Name = ?, Phone = ?, Address = ? WHERE UserID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $Name, $Phone, $Address, $UserID);
    $update_stmt->execute();

    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa người dùng</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../template/css/user.css">
</head>
<body>
    <div class="form-container">
    <h1 class="page-header">Chỉnh sửa thông tin người dùng</h1>
    <form method="post">
        <div class="form-group">
            <label>Tên:</label>
            <input type="text" name="Name" value="<?= $user['Name'] ?>" required>
        </div>
        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="Phone" value="<?= $user['Phone'] ?>" required>
        </div>
        <div class="form-group">
            <label>Địa chỉ:</label>
            <input type="text" name="Address" value="<?= $user['Address'] ?>" required>
        </div>
        <div class="button-container">
            <button type="submit" class="btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</div>
</body>
</html>
