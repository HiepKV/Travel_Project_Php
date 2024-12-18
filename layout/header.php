<?php
include("../includes/connect.php");

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
<link rel="stylesheet" href="../template/css/header.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
        #webName {
            color: pink;
            /* Màu hồng ban đầu */
            transition: color 1s ease;
            /* Hiệu ứng chuyển màu mượt mà */
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    let webName = document.getElementById("webName");
    setInterval(function() {
        // Lấy màu sắc hiện tại của phần tử từ CSS
        let currentColor = window.getComputedStyle(webName).color;
        
        // Kiểm tra màu hiện tại và thay đổi
        if (currentColor === "rgb(255, 192, 203)") {  // Màu hồng (pink)
            webName.style.color = "white";  // Màu white
        } else {
            webName.style.color = "pink";  // Màu hồng
        }
    }, 2000);  // Chuyển đổi mỗi 2 giây
});
    </script>
<header>
    <div id="banner">
        <div class="logo-container">
            <div class="logo">
                <img id="logo" src="/projectphp/img/indeximg/banner.png">
            </div>
            <div class="web_name">
                <h2 id=webName>TravelTrip</h2>
                <p><i class="fa-solid fa-plane"></i> Bạn muốn đi đâu?</p>
            </div>
        </div>
        <div class="menu">
            <ul>
                <li style="width: 140px;"><a href="http://localhost/projectphp/User_dash/dashboard.php"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
                <ul>
                    <?php if (isset($_SESSION['UserID'])): ?>
                        <li><a href="http://localhost/projectphp/feedback/feedback.php"><i class="fa-solid fa-layer-group"></i> Đánh giá</a></li>
                        <li><a href="http://localhost/projectphp/booking/my_tour.php"><i class="fa-solid fa-newspaper"></i> Tour</a></li>
                    <?php else: ?>
                        <!-- Nếu chưa đăng nhập, hiển thị thông báo -->
                        <li><a href="javascript:void(0)" onclick="showLoginAlert();"><i class="fa-solid fa-layer-group"></i> Đánh giá</a></li>
                        <li><a href="javascript:void(0)" onclick="showLoginAlert();"><i class="fa-solid fa-newspaper"></i> Tour</a></li>
                    <?php endif; ?>

                </ul>


                <?php if (isset($user['Email']) && !empty($user['Email'])): ?>
                    <!-- Nếu người dùng đã đăng nhập, hiển thị menu tài khoản -->
                    <li id="menu">
                        <a href="#" class="user-icon">
                            <i class="fa-solid fa-user"></i>
                            <?php echo htmlspecialchars($user['Name']); ?>
                        </a>
                        <ul class="submenu">
                            <li><a href="../logged_in_user/profile.php">Xem</a></li>
                            <li><a href="http://localhost/projectphp/auth/ChangeMK.php">Đổi mật khẩu</a></li>
                            <li><a href="../logged_in_user/logout.php" onclick="logout()">Đăng Xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Nếu người dùng chưa đăng nhập, hiển thị form đăng nhập -->
                    <li class="taikhoan"><a id="accountLink"><i class="fa-solid fa-user"></i> Tài Khoản</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="form_Account">
        <button type="button" style="background-color: rgb(0, 228, 255);" onclick="window.location.href='http://localhost/projectphp/auth/login.php'">Bạn đã có tài khoản</button>
        <p>Bạn chưa có tài khoản? <a href="http://localhost/projectphp/auth/register.php">Đăng ký</a> ngay</p>
    </div>
    <div class="bamien">
        <div id="mb"><a href="http://localhost/projectphp/logged_in_user/mienbac.php">MIỀN BẮC</a></div>
        <div id="mt"><a href="http://localhost/projectphp/logged_in_user/mientrung.php">MIỀN TRUNG</a></div>
        <div id="mn"><a href="http://localhost/projectphp/logged_in_user/miennam.php">MIỀN NAM</a></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function showLoginAlert() {
        Swal.fire({
            title: 'Bạn chưa đăng nhập!',
            text: "Bạn cần đăng nhập để thực hiện hành động này.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#159895', 
            cancelButtonColor: '#159895', 
            confirmButtonText: 'OK',
            cancelButtonText: 'Hủy',
            
           
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng người dùng đến trang login
                window.location.href = 'http://localhost/projectphp/auth/login.php';
            }
        });
    }
</script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const accountLink = document.getElementById('accountLink');
            const formAccount = document.querySelector('.form_Account');

            // Kiểm tra tồn tại của phần tử
            if (!accountLink || !formAccount) {
                return; // Thoát khỏi hàm nếu không tìm thấy phần tử
            }

            formAccount.style.display = 'none';
            accountLink.addEventListener('click', (event) => {
                event.preventDefault();
                if (formAccount.style.display === 'none') {
                    formAccount.style.display = 'block';
                } else {
                    formAccount.style.display = 'none';
                }
            });

            document.addEventListener('click', (event) => {
                if (!accountLink.contains(event.target) && !formAccount.contains(event.target)) {
                    formAccount.style.display = 'none';
                }
            });

            formAccount.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });
        // document.addEventListener('DOMContentLoaded', () => {
        //     const accountLink = document.getElementById('accountLink');
        //     const formAccount = document.querySelector('.form_Account');
        //     formAccount.style.display = 'none';
        //     accountLink.addEventListener('click', (event) => {
        //         event.preventDefault();
        //         if (formAccount.style.display === 'none') {
        //             formAccount.style.display = 'block';
        //         } else {
        //             formAccount.style.display = 'none';
        //         }
        //     });

        //     document.addEventListener('click', (event) => {
        //         if (!accountLink.contains(event.target) && !formAccount.contains(event.target)) {
        //             formAccount.style.display = 'none';
        //         }
        //     });
        //     formAccount.addEventListener('click', (event) => {
        //         event.stopPropagation();
        //     });
        // });
    </script>
</header>