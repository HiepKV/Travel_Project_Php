<?php
include("../includes/connect.php");
// Xử lý lọc theo trạng thái
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

// Xử lý cập nhật trạng thái
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_booking_tour'], $_POST['status'])) {
    $id_booking_tour = intval($_POST['id_booking_tour']);
    $status = $_POST['status'];

    $check_status_query = "SELECT status FROM booking_tour WHERE id_booking_tour = ?";
    $check_status_stmt = $conn->prepare($check_status_query);
    $check_status_stmt->bind_param("i", $id_booking_tour);
    $check_status_stmt->execute();
    $result = $check_status_stmt->get_result();
    $booking = $result->fetch_assoc();

    if ($booking['status'] == 'Đã hủy') {
        echo "<script>alert('Tour này đã bị hủy, không thể cập nhật trạng thái nữa!');</script>";
    } else {
        $sql_update = "UPDATE booking_tour SET status = ? WHERE id_booking_tour = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $status, $id_booking_tour);

        if ($stmt->execute()) {
            echo "<script>alert('Cập nhật trạng thái thành công!');</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi cập nhật trạng thái.');</script>";
        }
        $stmt->close();
    }
    $check_status_stmt->close();
}

// Truy vấn dữ liệu không phân trang
$search_status = '%' . $status_filter . '%';
$sql = "SELECT bt.*, t.tour_name, u.Name AS customer_name 
        FROM booking_tour bt
        JOIN Tours t ON bt.tour_id = t.tour_id
        JOIN users u ON bt.UserID = u.UserID
        WHERE bt.status LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search_status);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt tour</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Quản Lý Đặt Tour</h1>
            </div>
            <div class="px-6 py-4 bg-gray-50">
                <form method="POST" class="flex space-x-4 items-center">
                    <label for="status_filter" class="text-gray-700 font-medium">Lọc theo trạng thái:</label>
                    <select name="status_filter" id="status_filter" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="Đang chờ" <?php echo $status_filter === 'Đang chờ' ? 'selected' : ''; ?>>Đang chờ</option>
                        <option value="Đã xác nhận" <?php echo $status_filter === 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="Đã hủy" <?php echo $status_filter === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300 flex items-center">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                </form>
            </div>

            <!-- Booking Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <?php 
                            $headers = ['Mã đặt tour', 'Tên tour', 'Khách hàng', 'Số điện thoại', 'Email', 'Địa chỉ', 'Số lượng', 'Tổng tiền', 'Thời gian đặt', 'Trạng thái', 'Hành động'];
                            foreach($headers as $header): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $header; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap"><?php echo $row['id_booking_tour']; ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['tour_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['SDT']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['Email']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['DiaChi']); ?></td>
                                    <td class="px-6 py-4"><?php echo $row['so_luong']; ?></td>
                                    <td class="px-6 py-4"><?php echo number_format($row['money'], 0, ',', '.'); ?> VNĐ</td>
                                    <td class="px-6 py-4"><?php echo $row['thoi_gian_dat']; ?></td>
                                    <td class="px-6 py-4">
                                        <span class="
                                            <?php 
                                            switch($row['status']) {
                                                case 'Đang chờ': echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'Đã xác nhận': echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'Đã hủy': echo 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                            ?> 
                                            px-2 py-1 rounded-full text-xs font-medium">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="" class="flex flex-col space-y-2">
                                            <input type="hidden" name="id_booking_tour" value="<?php echo $row['id_booking_tour']; ?>">
                                            <select name="status" 
                                                    class="px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="Đang chờ" <?php echo $row['status'] === 'Đang chờ' ? 'selected' : ''; ?>>Đang chờ</option>
                                                <option value="Đã xác nhận" <?php echo $row['status'] === 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                <option value="Đã hủy" <?php echo $row['status'] === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                            </select>
                                            <button type="submit" 
                                                    class="bg-blue-500 text-white px-2 py-1 rounded-md hover:bg-blue-600 transition duration-300 text-xs">
                                                <i class="fas fa-sync mr-1"></i>Cập nhật
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-info-circle mr-2"></i>Không có bản ghi nào phù hợp.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Optional: Add some client-side validation or interactivity
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const statusSelect = this.querySelector('select[name="status"]');
                if (!confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thành "${statusSelect.value}" không?`)) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>
<?php
$conn->close();
?>
