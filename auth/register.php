<?php
include 'C:\xampp\htdocs\projectphp\includes\connect.php';
// Xác thực mật khẩu
function validatePassword($matKhau) {
    if (strlen($matKhau) < 6) {
        return "Độ dài mật khẩu phải ít nhất 6 ký tự.";
    }
    $chuHoa = preg_match('@[A-Z]@', $matKhau);
    $chuThuong = preg_match('@[a-z]@', $matKhau);
    $So = preg_match('@[0-9]@', $matKhau);
    if (!$chuHoa || !$chuThuong || !$So) {
        return "Yêu cầu mật khẩu của bạn phải có cả chữ in hoa, chữ thường và số!";
    }
    return ''; // Không có lỗi
}

// Hàm đăng ký người dùng
function registerUser($conn, $name, $phone, $email, $address, $role, $password) {
    $stmt = $conn->prepare("INSERT INTO Users (Name, Role, Phone, Address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $role, $phone, $address);

    if ($stmt->execute()) {
        $userID = $stmt->insert_id; // Lấy ID người dùng mới
        $sql_account = "INSERT INTO Account (UserID, Email, Password) VALUES (?, ?, ?)";
        $stmt_account = $conn->prepare($sql_account);
        $stmt_account->bind_param("iss", $userID, $email, $password);

        return $stmt_account->execute();
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role = 'Customer';

    // Kiểm tra email đã tồn tại hay chưa
    $check_email_query = "SELECT * FROM Account WHERE Email = ?";
    $stmt_check = $conn->prepare($check_email_query);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Email đã được sử dụng. Vui lòng chọn email khác!');</script>";
    } else {
        $password_error = validatePassword($_POST['password'], $_POST['confirm_password']);
        if ($password_error) {
            echo "<script>alert('$password_error');</script>";
        } else {
                if (registerUser($conn, $name, $phone, $email, $address, $role, $password)) {
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                showSuccessModal();
                            });
                          </script>";
                } else {
                    echo "<script>alert('Lỗi đăng ký tài khoản.');</script>";
                }
            }
        }
    }
    // $stmt_check->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
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

.register-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 1.5rem;  /* Giảm padding */
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;  /* Giảm max-width để form nhỏ hơn */
    position: relative;
    backdrop-filter: blur(10px);
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

.form-title {
    color: #333;
    font-size: 1.6rem;  /* Giảm kích thước font */
    font-weight: 600;
    text-align: center;
    margin-bottom: 1rem;  /* Giảm khoảng cách giữa tiêu đề và form */
    position: relative;
}

.form-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: var(--primary-color);
    margin: 8px auto;
    border-radius: 2px;
}

.form-floating {
    margin-bottom: 1rem;  /* Giảm margin */
}

.form-floating>.form-control {
    padding: 0.8rem 0.75rem;  /* Giảm padding của input */
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
}

.btn-register {
    background-color: var(--primary-color);
    color: white;
    padding: 0.8rem;
    border: none;
    border-radius: 5px;
    width: 100%;
    font-size: 1rem;  /* Giảm kích thước font */
    font-weight: 500;
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.btn-register:hover {
    background-color: var(--hover-color);
    transform: translateY(-2px);
}

.input-group-text {
    background-color: transparent;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    z-index: 10;
}

.login-link {
    text-align: center;
    margin-top: 1rem;  /* Giảm margin */
    color: #666;
}

.login-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}

/* Modal styles */
.success-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    position: relative;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-100px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.success-icon {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .register-container {
        padding: 1rem;
    }

    .form-title {
        font-size: 1.5rem;
    }
}

    </style>
</head>
<?php include('../layout/header.php') ?>
<body>
    <div class="box"> <div class="register-container">
        <h1 class="form-title">Đăng Ký Tài Khoản</h1>
        <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Mật khẩu</label>

            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <label for="confirm_password"><i class="fas fa-lock me-2"></i>Xác nhận mật khẩu</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                <label for="name"><i class="fas fa-user me-2"></i>Họ và tên</label>
            </div>

            <div class="form-floating">
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                <label for="phone"><i class="fas fa-phone me-2"></i>Số điện thoại</label>
            </div>

            <div class="form-floating">
                <textarea class="form-control" id="address" name="address" placeholder="Address" style="height: 100px" required></textarea>
                <label for="address"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ</label>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus me-2"></i>Đăng Ký
            </button>

            <div class="login-link">
                Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
            </div>
        </form>
    </div>
</div>
   
    <!-- thông báo khi đăng ký thành công -->
    <div class="success-modal" id="successModal">
        <div class="modal-content">
            <i class="fas fa-check-circle success-icon"></i>
            <h2>Đăng Ký Thành Công!</h2>
            <p>Tài khoản của bạn đã được tạo thành công.</p>
            <button class="btn btn-register mt-3" onclick="redirectToLogin()">
                Đăng Nhập Ngay
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSuccessModal() {
            document.getElementById('successModal').style.display = 'flex';
        }

        function redirectToLogin() {
            window.location.href = 'login.php';
        }

        // mắt cho mật khẩu 
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.createElement('i');
            togglePassword.className = 'fas fa-eye password-toggle';
            passwordInput.parentElement.appendChild(togglePassword);

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.className = `fas fa-eye${type === 'password' ? '' : '-slash'} password-toggle`;
            });
        });
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Mật khẩu và xác nhận mật khẩu không khớp!');
                return false;
            }

            const phone = document.getElementById('phone').value;
            const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;

            if (!phoneRegex.test(phone)) {
                event.preventDefault();
                alert('Vui lòng nhập số điện thoại hợp lệ!');
                return false;
            }
        });
        // Form validation
        // document.getElementById('registerForm').addEventListener('submit', function(event) {
        //     const phone = document.getElementById('phone').value;
        //     const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;

        //     if (!phoneRegex.test(phone)) {
        //         event.preventDefault();
        //         alert('Vui lòng nhập số điện thoại hợp lệ!');
        //         return false;
        //     }
        // });
    </script>
</body>

</html>