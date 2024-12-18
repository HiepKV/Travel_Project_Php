<?php
include("../includes/connect.php");

// Kiểm tra và gán giá trị mặc định
$page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
$filter_tour = isset($_GET['tour']) ? $_GET['tour'] : '';
$filter_rating = isset($_GET['rating']) ? $_GET['rating'] : '';

$limit = 3;
$offset = ($page_number - 1) * $limit;

// Truy vấn feedback
$query = "SELECT 
    fb.id_fb, 
    t.tour_name, 
    u.UserID,
    u.Name as user_name, 
    u.Phone as user_phone,
    a.Email as user_email,
    fb.feedback, 
    fb.rating, 
    fb.created_at 
FROM 
    feed_back fb
JOIN 
    Tours t ON fb.tour_id = t.tour_id
JOIN 
    users u ON fb.UserID = u.UserID
JOIN 
    account a ON u.UserID = a.UserID
WHERE 1=1";

// Thêm điều kiện lọc
if (!empty($filter_tour)) {
    $query .= " AND t.tour_name LIKE '%" . mysqli_real_escape_string($conn, $filter_tour) . "%'";
}

if (!empty($filter_rating)) {
    $query .= " AND fb.rating = " . (int)$filter_rating;
}

$query .= " ORDER BY fb.created_at DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Đếm tổng số feedback
$count_query = "SELECT COUNT(*) as total FROM feed_back fb
                JOIN Tours t ON fb.tour_id = t.tour_id
                JOIN users u ON fb.UserID = u.UserID
                JOIN account a ON u.UserID = a.UserID
                WHERE 1=1";

// Thêm điều kiện lọc
if (!empty($filter_tour)) {
    $count_query .= " AND t.tour_name LIKE '%" . mysqli_real_escape_string($conn, $filter_tour) . "%'";
}

if (!empty($filter_rating)) {
    $count_query .= " AND fb.rating = " . (int)$filter_rating;
}

$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_feedbacks = $count_row['total'];

// Số trang
$total_pages = ceil($total_feedbacks / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Feedback Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .rating-stars .filled {
            color: #ffc107;
        }
        .rating-stars .empty {
            color: #e0e0e0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Quản Lý Feedback</h1>
            </div>

            <!-- Filter Section -->
            <div class="px-6 py-4 bg-gray-50">
                <form method="GET" action="dashboard.php" class="flex space-x-4">
                    <input type="hidden" name="page" value="reviews">
                    <input type="text" name="tour" 
                           class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Lọc theo tên tour"
                           value="<?php echo isset($_GET['tour']) ? htmlspecialchars($_GET['tour']) : ''; ?>">
                    
                    <select name="rating" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Chọn đánh giá</option>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" 
                                    <?php echo isset($_GET['rating']) && $_GET['rating'] == $i ? 'selected' : ''; ?>>
                                <?php echo $i; ?> Sao
                            </option>
                        <?php endfor; ?>
                    </select>
                    
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                    <a href="dashboard.php?page=reviews" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300">
                        <i class="fas fa-sync-alt mr-2"></i>Làm mới
                    </a>
                </form>
            </div>

            <!-- Filter Result Alert -->
            <?php if (!empty($filter_tour) || !empty($filter_rating)): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">Kết quả lọc:</p>
                    <p>
                        <?php 
                        if (!empty($filter_tour)) echo "Tour: " . htmlspecialchars($filter_tour) . " ";
                        if (!empty($filter_rating)) echo "Đánh giá: " . $filter_rating . " sao";
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Feedback Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <?php 
                            $headers = ['ID', 'Tour', 'UserID', 'User Phone', 'Email', 'Người Đánh Giá', 'Nội Dung', 'Đánh Giá', 'Ngày Tạo', 'Hành Động'];
                            foreach($headers as $header): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $header; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $row['id_fb']; ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['tour_name']); ?></td>
                                <td class="px-6 py-4"><?php echo $row['UserID']; ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['user_phone']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['feedback']); ?></td>
                                <td class="px-6 py-4 rating-stars">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $row['rating'] 
                                            ? '<i class="fas fa-star filled"></i>' 
                                            : '<i class="far fa-star empty"></i>';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4"><?php echo $row['created_at']; ?></td>
                                <td class="px-6 py-4">
                                    <button onclick="confirmDelete(<?php echo $row['id_fb']; ?>)" 
                                            class="text-red-500 hover:text-red-700 transition duration-300">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-white flex justify-center">
                <nav>
                    <ul class="flex space-x-2">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="dashboard.php?page=reviews
                                    <?php 
                                    echo !empty($filter_tour) ? '&tour=' . urlencode($filter_tour) : '';
                                    echo !empty($filter_rating) ? '&rating=' . $filter_rating : ''; 
                                    echo '&page_number=' . $i;
                                    ?>" 
                                   class="px-3 py-2 <?php echo $page_number == $i ? 'bg-blue-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'; ?> 
                                   border border-gray-300 rounded-md">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id) {
        if (confirm('Bạn có chắc chắn muốn xóa feedback này không?')) {
            window.location.href = 'delete_feedback.php?id=' + id;
        }
    }
    </script>
</body>
</html>
