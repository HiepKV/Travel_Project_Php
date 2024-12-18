<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/projectphp/includes/connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT Account.*, Users.Role, Users.Name 
            FROM Account 
            JOIN Users ON Account.UserID = Users.UserID 
            WHERE Email=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Role'] = $user['Role'];
            $_SESSION['Name'] = $user['Name'];

            if ($user['Role'] === 'Admin') {
                header("Location: /projectphp/admin/dashboard.php");
            } else {
                header("Location: /projectphp/User_dash/dashboard.php");
            }
            exit;
        } else {
            $error = "Thông tin đăng nhập không chính xác!";
        }
    } else {
        $error = "Thông tin đăng nhập không chính xác!";
    }
}
?>
<?php include('../layout/header.php') ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Dulich3mien.vn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1A5F7A;
            --secondary-color: #159895;
            --accent-color: #57C5B6;
            --light-background: #F8F9FA;
            --text-color: #333;
        }


        .box {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            /* Giảm padding */
            border-radius: 12px;
            /* Làm bo góc nhỏ hơn */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            /* Giảm max-width */
            position: relative;
            backdrop-filter: blur(10px);
            align-items: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
            /* Giảm khoảng cách dưới */
        }

        .login-header img {
            width: 150px;
            /* Giảm kích thước logo */
            height: auto;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .login-header img:hover {
            transform: scale(1.05);
        }

        .login-header h1 {
            color: var(--text-color);
            font-size: 1.6rem;
            /* Giảm kích thước tiêu đề */
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
            /* Giảm kích thước chữ phụ */
        }

        .form-floating {
            position: relative;
            margin-bottom: 1rem;
            /* Giảm khoảng cách giữa các trường */
        }

        .form-floating input {
            width: 100%;
            padding: 0.8rem 2rem 0.8rem 2rem;
            /* Giảm padding trong input */
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            /* Giảm kích thước chữ trong input */
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            outline: none;
        }

        .form-floating label {
            position: absolute;
            left: 2rem;
            /* Điều chỉnh vị trí label */
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
            color: #666;
            pointer-events: none;
        }

        .form-floating input:focus~label,
        .form-floating input:not(:placeholder-shown)~label {
            top: 0;
            left: 1rem;
            font-size: 0.8rem;
            padding: 0 0.5rem;
            background-color: white;
            color: var(--primary-color);
        }

        .error-message {
            background-color: #fff2f2;
            color: var(--error-color);
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            /* Giảm kích thước chữ thông báo lỗi */
            border-left: 4px solid var(--error-color);
        }

        .login-btn {
            width: 100%;
            padding: 0.8rem;
            /* Giảm padding của button */
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            /* Giảm font size */
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-btn:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: right;
            margin: 0.8rem 0;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            /* Giảm kích thước chữ link */
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--hover-color);
            text-decoration: underline;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            color: #666;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--hover-color);
            text-decoration: underline;
        }

        .back-home {
            position: fixed;
            top: 2rem;
            left: 2rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.2rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .back-home i {
            font-size: 1.2rem;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
                /* Giảm padding ở mobile */
                margin: 1rem;
            }

            .login-header h1 {
                font-size: 1.4rem;
            }

            .back-home {
                top: 1rem;
                left: 1rem;
                padding: 0.6rem 1rem;
            }
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
            z-index: 2;
            padding: 5px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        #password {
            padding-right: 3rem !important;
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="login-container">
            <div class="login-header">
                <img src="/projectphp/img/indeximg/banner.png" alt="Logo">
                <h1>Chào mừng trở lại!</h1>
                <p>Đăng nhập để tiếp tục với Dulich3mien.vn</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder=" " required>
                    <label for="email">Email của bạn</label>
                </div>

                <div class="form-floating">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Mật khẩu</label>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <div class="forgot-password">
                    <a href="http://localhost/projectphp/auth/forgot_password.php">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Đăng nhập
                </button>

                <div class="register-link">
                    Chưa có tài khoản?
                    <a href="register.php">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>


    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.login-header img');
            logo.style.opacity = '0';
            logo.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                logo.style.transition = 'all 0.5s ease';
                logo.style.opacity = '1';
                logo.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>

</html>