<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
//     header('Location: ../auth/login.php');
//     exit();
// }


?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            position: fixed;
            /* Đặt sidebar cố định */
            top: 0;
            /* Cố định ở phía trên */
            left: 0;
            /* Cố định ở phía trái */
            background: linear-gradient(to bottom right, #2c3e50, #34495e);
            width: 256px;
            color: #ecf0f1;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar-logo {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.25rem;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .sidebar-logo img {
            max-height: 40px;
            transition: transform 0.3s ease;
        }

        .sidebar-logo img:hover {
            transform: scale(1.1);
        }

        .sidebar-nav ul {
            padding: 0;
        }

        .sidebar-nav ul li {
            margin-bottom: 0.5rem;
            position: relative;
        }

        .sidebar-nav ul li a {
            color: #bdc3c7;
            display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            border-radius: 0.625rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav ul li a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            transition: width 0.3s ease;
            z-index: 0;
        }

        .sidebar-nav ul li a:hover::before {
            width: 100%;
        }

        .sidebar-nav ul li a i {
            margin-right: 0.875rem;
            width: 1.5rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav ul li a span {
            position: relative;
            z-index: 1;
        }

        .active-nav-item {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
        }

        .header {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e7eaf3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
        }

        .flex-1 {
            margin-left: 256px;

        }

        .content-area {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e7eaf3;
            margin: 1.5rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .content-area:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            background-color: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
        }

        .user-profile span {
            color: #2c3e50;
            font-weight: 500;
        }

        .logout-btn {
            color: #e74c3c;
            font-weight: 600;
            transition: all 0.3s ease;
            background-color: rgba(231, 76, 60, 0.1);
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .logout-btn:hover {
            color: #ffffff;
            background-color: #e74c3c;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }

            .sidebar-nav ul li a span {
                display: none;
            }

            .sidebar-nav ul li a {
                justify-content: center;
            }
        }

        /* Scroll bar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #2c3e50;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #34495e;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex">
        <div class="sidebar w-64 bg-white shadow-md h-screen">
            <div class="sidebar-logo p-4 border-b">
                <img src="/projectphp/img/indeximg/banner.png" alt="Logo" class="h-10 mx-auto">
            </div>
            <nav class="sidebar-nav mt-4">
                <ul>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer <?php echo $page == 'dashboard' ? 'active-nav-item' : ''; ?>">
                        <a href="?page=dashboard" class="flex items-center">
                            <i class="fas fa-home mr-2"></i>
                            <span>Tổng quan</span>
                        </a>
                    </li>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer <?php echo $page == 'user' ? 'active-nav-item' : ''; ?>">
                        <a href="?page=user" class="flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            <span>Quản lí người dùng</span>
                        </a>
                    </li>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer <?php echo $page == 'tours' ? 'active-nav-item' : ''; ?>">
                        <a href="?page=tours" class="flex items-center">
                            <i class="fas fa-bus-alt mr-2"></i>
                            <span>Quản lý Tour</span>
                        </a>
                    </li>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer <?php echo $page == 'reviews' ? 'active-nav-item' : ''; ?>">
                        <a href="?page=reviews" class="flex items-center">
                            <i class="fas fa-star mr-2"></i>
                            <span>Quản lí đánh giá</span>
                        </a>
                    </li>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer <?php echo $page == 'bookings' ? 'active-nav-item' : ''; ?>">
                        <a href="?page=booking" class="flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            <span>Quản lý Đặt Tour</span>
                        </a>
                    </li>
                    <li class="p-2 hover:bg-gray-100 cursor-pointer ">
                        <a href="http://localhost/projectphp/User_dash/dashboard.php" class="flex items-center" target="_blank">
                            <i class="fas fa-cog mr-2"></i>
                            <span>Trang người dùng</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="flex-1">
            <header class="header bg-white shadow-md p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
                <div class="user-profile">
                    <span class="mr-4"><?php echo $_SESSION['Name']; ?></span>
                    <a href="../auth/logout.php" class="logout-btn">Đăng xuất</a>
                </div>
            </header>
            <main class="content-area m-6 p-6 bg-white rounded-lg shadow-md">
                <?php
                // Lấy thông tin về page và action từ URL
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                $action = isset($_GET['action']) ? $_GET['action'] : 'list';



                // Điều hướng theo page
                switch ($page) {
                    case 'dashboard':
                        include 'overview.php';
                        break;
                    case 'booking':
                        include 'booking.php';
                        break;
                    case 'tours':
                        $filter_tour = isset($_GET['tour']) ? $_GET['tour'] : '';
                        $filter_rating = isset($_GET['rating']) ? $_GET['rating'] : '';
                        switch ($action) {
                            case 'tour':
                                include '../tours/tour.php';
                                break;
                            case 'add':
                                include '../tours/add.php';
                                break;
                            case 'edit':
                                include '../tours/edit.php';
                                break;
                            case 'delete':
                                include '../tours/delete.php';
                                break;
                            default:
                                include '../tours/tour.php';
                        }
                        break;
                    case 'user':
                        switch ($action) {
                            case 'list':
                                include '../user/list.php';
                                break;
                            case 'role':
                                include '../user/role.php';
                                break;
                        }
                        break;
                    case 'reviews':
                        include 'feedback.php';
                        break;

                    default:
                        include 'dashboard.php';
                }
                ?>
            </main>

        </div>
    </div>
</body>

</html>