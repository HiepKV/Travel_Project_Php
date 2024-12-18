<?php
ob_start();
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/projectphp/includes/connect.php';
include('../layout/header.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['UserID'])) {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['UserID'];
$error = '';
$success = '';
$username = $_SESSION['Name']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Lấy thông tin mật khẩu
    $query = "SELECT Password FROM account WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Kiểm tra tồn tại tài khoản
    if ($result->num_rows > 0) {
        $account = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (!password_verify($current_password, $account['Password'])) {
            $error = "Mật khẩu hiện tại không đúng.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        } elseif (strlen($new_password) < 6) {
            $error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update_query = "UPDATE account SET Password = ? WHERE UserID = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                header("Location: ./login.php");
                exit();
            } else {
                $error = "Có lỗi xảy ra. Vui lòng thử lại.";
            }
        }
    } else {
        $error = "Không tìm thấy thông tin tài khoản.";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi Mật Khẩu - <?= htmlspecialchars($username) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../template/css/doimk.css">
</head>
<body>
    <div class="container">
        <div class="change-password-container">
            <h2>Đổi Mật Khẩu cho Người Dùng: <?= htmlspecialchars($username) ?></h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" id="changePasswordForm">
                <div class="form-group password-wrapper">
                    <label for="current_password">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="form-group password-wrapper">
                    <label for="new_password">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                    <div id="password-strength" class="password-strength"></div>
                </div>
                <div class="form-group password-wrapper">
                    <label for="confirm_password">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-change-password">Đổi Mật Khẩu</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Thêm tính năng ẩn/hiện mật khẩu cho từng trường
            ['current_password', 'new_password', 'confirm_password'].forEach(function(inputId) {
                const passwordInput = document.getElementById(inputId);
                const togglePassword = document.createElement('i');
                togglePassword.className = 'fas fa-eye password-toggle';
                passwordInput.parentElement.appendChild(togglePassword);

                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.className = `fas fa-eye${type === 'password' ? '' : '-slash'} password-toggle`;
                });
            });

            // Kiểm tra độ mạnh mật khẩu
            document.getElementById('new_password').addEventListener('input', function() {
                const password = this.value;
                const strengthDiv = document.getElementById('password-strength');
                
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]+/)) strength++;
                if (password.match(/[A-Z]+/)) strength++;
                if (password.match(/[0-9]+/)) strength++;
                if (password.match(/[$@#&!]+/)) strength++;
                
                strengthDiv.innerHTML = strength < 2 ? 'Mật khẩu yếu' :
                                        strength < 4 ? 'Mật khẩu trung bình' : 
                                        'Mật khẩu mạnh';
                
                strengthDiv.className = 'password-strength ' + 
                    (strength < 2 ? 'password-weak' : 
                     strength < 4 ? 'password-medium' : 
                     'password-strong');
            });

            // Kiểm tra xác nhận mật khẩu
            document.getElementById('confirm_password').addEventListener('input', function() {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = this.value;
                
                if (newPassword !== confirmPassword) {
                    this.setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>