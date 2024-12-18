<?php
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

// kiểm tra mật khẩu mới không được trùng mật khẩu cũ
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

    // quên mật khẩu
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $stmt = $conn->prepare("SELECT AccountID FROM account WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $stmt = $conn->prepare("UPDATE account SET reset_token = ?, token_expiry = ? WHERE Email = ?");
            $stmt->bind_param("sss", $token, $expiry, $email);  
            $stmt->execute();

            echo "
<div class='notification-container'>
    <div class='notification success'>
        <div class='notification-icon'>✉️</div>
        <div class='notification-content'>
            <h3>Yêu cầu đặt lại mật khẩu</h3>
            <p>Một liên kết đặt lại mật khẩu đã được gửi đến địa chỉ email của bạn.</p>
            <div class='notification-link'>
                <a href='?token=$token'>Nhấp vào đây để đặt lại mật khẩu</a>
            </div>
            <small>Liên kết sẽ hết hạn sau 1 giờ</small>
        </div>
    </div>
</div>
";
        } else {
            echo "
<div class='notification-container'>
    <div class='notification success'>
        <div class='notification-icon'>✉️</div>
        <div class='notification-content'>
            <p>Email không tồn tại trong hệ thống.</p>
            </div>
            <small>Liên kết sẽ hết hạn sau 1 giờ</small>
        </div>
    </div>
</div>
";
        }
    }
    // đặt lại mật khẩu
    elseif (isset($_POST['password']) && isset($_POST['token'])) {
        $token = $_POST['token'];
        $new_password = $_POST['password'];

        // Kiểm tra độ mạnh mật khẩu
        $passwordErrors = validatePassword($new_password);
        if (!empty($passwordErrors)) {
            echo "<p>Lỗi mật khẩu:</p><ul>";
            foreach ($passwordErrors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("SELECT AccountID FROM account WHERE reset_token = ? AND token_expiry > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $account_id = $row['AccountID'];

                // mật khẩu mới không được trùng mật khẩu cũ
                if (isNewPasswordDifferent($conn, $account_id, $new_password)) {
                    $stmt = $conn->prepare("UPDATE account SET Password = ?, reset_token = NULL, token_expiry = NULL WHERE AccountID = ?");
                    $stmt->bind_param("si", $hashed_password, $account_id);
                    $stmt->execute();

                    echo "<div class='notification-container'>
    <div class='notification success'>
        <div class='notification-icon'>✉️</div>
        <div class='notification-content'>
        <p>Mật khẩu đã được đặt lại thành công!</p>
            </div>
            <small>Liên kết sẽ hết hạn sau 1 giờ</small>
        </div>
    </div>
</div>
";
                    header("Location: login.php");
                    exit();
                } else {
                    echo "<div class='notification-container'>
    <div class='notification success'>
        <div class='notification-icon'>✉️</div>
        <div class='notification-content'>
           <p>Mật khẩu mới không được trùng với mật khẩu cũ!</p>
            </div>
            <small>Liên kết sẽ hết hạn sau 1 giờ</small>
        </div>
    </div>
</div>";
                }
            } else {
                echo "<div class='notification-container'>
    <div class='notification success'>
        <div class='notification-icon'>✉️</div>
        <div class='notification-content'>
           <p>Token không hợp lệ hoặc đã hết hạn.</p>
            </div>
            <small>Liên kết sẽ hết hạn sau 1 giờ</small>
        </div>
    </div>
</div>
";
            }
        }
    }
}
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
                <button type="submit">Gửi yêu cầu</button>
            </form>';
        } elseif (isset($_GET['token'])) {
            $token = $_GET['token'];
            echo "
            <h2>Đặt lại mật khẩu</h2>
            <form method='POST' action=''>
                <input type='hidden' name='token' value='$token'>
                <label for='password'>Mật khẩu mới:</label>
                <input type='password' id='password' name='password' required>
                <button type='submit'>Đặt lại mật khẩu</button>
            </form>";
        }
        ?>
    </div>
</div>

</html>