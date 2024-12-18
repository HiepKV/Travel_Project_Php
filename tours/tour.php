<?php
include 'C:\xampp\htdocs\projectphp\includes\connect.php';

if (isset($_POST['delete_selected'])) {
    if (!empty($_POST['tour_ids'])) {
        $tour_ids = $_POST['tour_ids'];
        $tour_ids_str = implode(',', $tour_ids);
        $sql = "DELETE FROM Tours WHERE tour_id IN ($tour_ids_str)";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Đã xóa thành công các tour!";
        } else {
            $_SESSION['error_message'] = "Lỗi khi xóa tour: " . $conn->error;
        }
    }
}
$sql = "SELECT * FROM Tours";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản Trị Điểm Đến</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Danh Sách Tour</h2>
                <button 
                    onclick="location.href='?page=tours&action=add'"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300"
                >
                    Thêm Tour Mới
                </button>
            </div>

            <?php
            // Display success or error messages
            if (isset($_SESSION['success_message'])) {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>" 
                     . htmlspecialchars($_SESSION['success_message']) . 
                     "</div>";
                unset($_SESSION['success_message']);
            }

            if (isset($_SESSION['error_message'])) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>" 
                     . htmlspecialchars($_SESSION['error_message']) . 
                     "</div>";
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="" method="POST" x-data="{ 
                selectAll: false, 
                selectedTours: [],
                toggleAll() {
                    this.selectedTours = this.selectAll ? <?= json_encode(array_column($result->fetch_all(MYSQLI_ASSOC), 'tour_id')) ?> : []
                }
            }">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input 
                                        type="checkbox" 
                                        x-model="selectAll" 
                                        @change="toggleAll()"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                    >
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Tour</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Tour</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô Tả</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương Tiện</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách Sạn</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khởi Hành</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vùng Miền</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $result->data_seek(0); // Reset result pointer
                            if ($result->num_rows > 0) {
                                while ($tour = $result->fetch_assoc()) {
                                    // Image handling (same as before)
                                    $imageHTML = '<img src="/projectphp/template/images/placeholder.jpg" alt="No Image" class="w-12 h-12 object-cover rounded">';
                                    if (!empty($tour['img'])) {
                                        $imageName = basename($tour['img']);
                                        $fullImagePath = 'C:/xampp/htdocs/projectphp/tours/uploads/' . $imageName;

                                        if (file_exists($fullImagePath)) {
                                            $imageHTML = "<img src='/projectphp/tours/uploads/{$imageName}' alt='Tour Image' class='w-12 h-12 object-cover rounded'>";
                                        }
                                    }
                            ?>
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-4 py-4">
                                        <input 
                                            type="checkbox" 
                                            name="tour_ids[]" 
                                            value="<?= $tour['tour_id'] ?>"
                                            x-model="selectedTours"
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                        >
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= $tour['tour_id'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= $tour['tour_name'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['mota'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?= number_format($tour['price'], 0, ',', '.') ?> VND</td>
                                    <td class="px-4 py-4"><?= $imageHTML ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['thoigian'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['transport'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['khachsan'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['departure_location'] ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?= $tour['vung_mien'] ?></td>
                                    <td class="px-4 py-4 flex space-x-2">
                                        <a 
                                            href='?page=tours&action=edit&id=<?= $tour['tour_id'] ?>' 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-100 px-2 py-1 rounded text-sm"
                                        >
                                            Sửa
                                        </a>
                                        <a 
                                            href='javascript:void(0);' 
                                            onclick="confirmDelete(<?= $tour['tour_id'] ?>)"
                                            class="text-red-600 hover:text-red-900 bg-red-100 px-2 py-1 rounded text-sm"
                                        >
                                            Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='12' class='text-center py-4 text-gray-500'>Không có tour nào được tìm thấy.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button 
                        type="submit" 
                        name="delete_selected" 
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300"
                        x-show="selectedTours.length > 0"
                    >
                        Xóa Các Tour Đã Chọn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(tour_id) {
            if (confirm('Bạn có chắc chắn muốn xóa tour này?')) {
                window.location.href = '../tours/delete.php?id=' + tour_id;
            }
        }
    </script>
</body>
</html>