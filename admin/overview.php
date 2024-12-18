
<?php
include '../includes/connect.php';

// Lấy số lượng tour
$result_tours = $conn->query("SELECT COUNT(*) AS total_tours FROM Tours");
$total_tours = $result_tours->fetch_assoc()['total_tours'];

// Lấy số lượng người dùng
$result_users = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $result_users->fetch_assoc()['total_users'];

// Lấy số lượng tour đã đặt
$result_bookings = $conn->query("SELECT COUNT(*) AS total_bookings FROM booking_tour");
$total_bookings = $result_bookings->fetch_assoc()['total_bookings'];

// Lấy thống kê phản hồi
$result_feedback = $conn->query("SELECT 
    SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) AS good_feedback,
    SUM(CASE WHEN rating < 4 THEN 1 ELSE 0 END) AS bad_feedback
    FROM feed_back");

$feedback_data = $result_feedback->fetch_assoc();
$good_feedback = $feedback_data['good_feedback'];
$bad_feedback = $feedback_data['bad_feedback'];

// Lấy tổng doanh thu
$result_revenue = $conn->query("SELECT SUM(money) AS total_revenue FROM booking_tour");
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];

// Lấy doanh thu tháng hiện tại
$current_month = date('Y-m');
$result_month_revenue = $conn->query("SELECT SUM(money) AS month_revenue FROM booking_tour WHERE thoi_gian_dat LIKE '$current_month%'");
$month_revenue = $result_month_revenue->fetch_assoc()['month_revenue'];

// Lấy tour phổ biến nhất
$result_popular_tour = $conn->query("
    SELECT t.tour_name, COUNT(b.tour_id) AS total_booked, SUM(b.money) AS revenue
    FROM booking_tour b
    JOIN Tours t ON b.tour_id = t.tour_id
    GROUP BY b.tour_id
    ORDER BY total_booked DESC
    LIMIT 1
");
$popular_tour = $result_popular_tour->fetch_assoc();

// Hoạt động gần đây
$recent_users = $conn->query("SELECT Name, RegistrationDate FROM users ORDER BY RegistrationDate DESC LIMIT 5");
$recent_bookings = $conn->query("
    SELECT u.Name AS user_name, t.tour_name, b.thoi_gian_dat 
    FROM booking_tour b 
    JOIN users u ON b.UserID = u.UserID
    JOIN Tours t ON b.tour_id = t.tour_id
    ORDER BY b.thoi_gian_dat DESC LIMIT 5
");
$recent_feedback = $conn->query("SELECT UserID, feedback, rating, created_at FROM feed_back ORDER BY created_at DESC LIMIT 5");

// Booking đang chờ xử lý
$result_pending = $conn->query("SELECT COUNT(*) AS pending_bookings FROM booking_tour WHERE status = 'Đang chờ'");
$pending_bookings = $result_pending->fetch_assoc()['pending_bookings'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <div class="bg-white shadow rounded-full px-4 py-2">
            <span class="text-gray-600">Xin chào, Admin</span>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tour Card -->
        <div class="bg-white shadow-lg rounded-2xl p-6 text-center card-hover gradient-bg text-white">
            <div class="mb-4">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Số Tour</h2>
            <p class="text-4xl font-bold"><?php echo $total_tours; ?></p>
        </div>

        <!-- User Card -->
        <div class="bg-white shadow-lg rounded-2xl p-6 text-center card-hover" style="background-color: #4ecdc4;">
            <div class="mb-4">
                <svg class="w-12 h-12 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2 text-white">Người Dùng</h2>
            <p class="text-4xl font-bold text-white"><?php echo $total_users; ?></p>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white shadow-lg rounded-2xl p-6 text-center card-hover" style="background-color: #ff6b6b;">
            <div class="mb-4">
                <svg class="w-12 h-12 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2 text-white">Doanh Thu</h2>
            <p class="text-2xl font-bold text-white"><?php echo number_format($total_revenue); ?> VND</p>
        </div>

        <!-- Popular Tour Card -->
        <div class="bg-white shadow-lg rounded-2xl p-6 text-center card-hover" style="background-color: #4834d4; color: white;">
            <div class="mb-4">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Tour Phổ Biến</h2>
            <p class="text-lg font-bold"><?php echo $popular_tour['tour_name']; ?></p>
            <p class="text-sm">Đã đặt: <?php echo $popular_tour['total_booked']; ?> lần</p>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="mt-10 bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Hoạt Động Gần Đây</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Recent Users -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Người Dùng Mới</h3>
                <ul class="space-y-2">
                    <?php while ($row = $recent_users->fetch_assoc()): ?>
                        <li class="bg-white p-2 rounded shadow-sm flex justify-between">
                            <span class="font-medium"><?php echo $row['Name']; ?></span>
                            <span class="text-sm text-gray-500"><?php echo $row['RegistrationDate']; ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Đơn Đặt Mới</h3>
                <ul class="space-y-2">
                    <?php while ($row = $recent_bookings->fetch_assoc()): ?>
                        <li class="bg-white p-2 rounded shadow-sm flex justify-between">
                            <span class="font-medium"><?php echo $row['user_name']; ?></span>
                            <span class="text-sm text-gray-500"><?php echo $row['tour_name']; ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Recent Feedback -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Đánh Giá Mới</h3>
                <ul class="space-y-2">
                    <?php while ($row = $recent_feedback->fetch_assoc()): ?>
                        <li class="bg-white p-2 rounded shadow-sm">
                            <div class="flex justify-between">
                                <span class="font-medium">User ID: <?php echo $row['UserID']; ?></span>
                                <span class="text-yellow-500">
                                    <?php 
                                    for($i = 0; $i < $row['rating']; $i++) {
                                        echo '★';
                                    }
                                    ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1"><?php echo $row['feedback']; ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="mt-6 bg-red-100 border-l-4 border-red-500 p-4">
        <div class="flex items-center">
            <svg class="w-8 h-8 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-xl font-bold text-red-700">Đang Chờ Xử Lý</h3>
                <p class="text-3xl font-bold text-red-600"><?php echo $pending_bookings; ?> Booking</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
