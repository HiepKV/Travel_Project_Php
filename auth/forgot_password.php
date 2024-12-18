<?php
ob_start();
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/projectphp/includes/connect.php';
include('../layout/header.php');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

function validatePassword($password)
{
    $errors = [];

    if (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải ít nhất 6 ký tự";
    }

    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Mật khẩu phải chứa ít nhất một chữ thường";
    }

    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Mật khẩu phải chứa ít nhất một số";
    }

    if (!preg_match("/[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]/", $password)) {
        $errors[] = "Mật khẩu phải chứa ít nhất một ký tự đặc biệt";
    }

    return $errors;
}

// Kiểm tra mật khẩu mới không được trùng mật khẩu cũ
function isNewPasswordDifferent($conn, $account_id, $new_password)
{
    $stmt = $conn->prepare("SELECT Password FROM account WHERE AccountID = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return !password_verify($new_password, $row['Password']);
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $stmt = $conn->prepare("SELECT AccountID FROM account WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['email'] = $_POST['email'];
            header("Location: ?token=$token");
            exit();
        } else {
            echo "<p>Email không tồn tại trong hệ thống.</p>";
        }
    } elseif (isset($_POST['password']) && isset($_POST['token'])) {
        $token = $_POST['token'];
        $new_password = $_POST['password'];

        $passwordErrors = validatePassword($new_password);
        if (!empty($passwordErrors)) {
            echo "<p>Lỗi mật khẩu:</p><ul>";
            foreach ($passwordErrors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $_SESSION['email'];
            $update_query = "UPDATE account SET Password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $hashed_password, $email);

            if ($update_stmt->execute()) {
                header("Location: ./login.php");
                exit();
            } else {
                $error = "Có lỗi xảy ra. Vui lòng thử lại.";
            }
        }
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="../template/css/fg_password.css">
</head>

<div class="body">
    <div class="container">
        <?php
        if (!isset($_GET['token'])) {
            echo '
            <h2>Quên Mật Khẩu</h2>
            <form method="POST" action="">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Xác nhận</button>
            </form>';
        } elseif (isset($_GET['token'])) {
            $token = $_GET['token'];
            echo "
            <h2>Đặt lại mật khẩu</h2>
            <form method='POST' action=''>
                <input type='hidden' name='token' value='$token'>
                <label for='password'>Mật khẩu mới:</label>
                <input type='password' id='password' name='password' required>
                <button type='submit'>Đặt lại</button>
            </form>";
        }
        ?>
    </div>
</div>

</html>
