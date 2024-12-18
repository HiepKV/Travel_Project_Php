<?php
session_start();
include('../layout/header.php');
include '../includes/connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['UserID'])) {
    // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['UserID'];

$query = "
    SELECT 
        bt.id_booking_tour, 
        t.tour_name, 
        t.img,
        bt.thoi_gian_dat, 
        bt.so_luong, 
        t.price AS tour_price,
        bt.money AS total_money,
        t.departure_location,
        bt.status,
        t.tour_id 
    FROM 
        booking_tour bt
    JOIN 
        Tours t ON bt.tour_id = t.tour_id
    WHERE 
        bt.UserID = ?
    ORDER BY 
        bt.thoi_gian_dat DESC
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Lỗi chuẩn bị câu truy vấn: ' . mysqli_error($conn));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Các Tour Đã Đặt</title>
    <link rel="stylesheet" href="../template/css/my_tour.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="my-tour-page">
        <div class="my-tour-container">
            <h2 class="my-tour-title">Danh Sách Các Tour Đã Đặt</h2>
            <?php if ($result->num_rows > 0): ?>
                <table class="my-tour-table">
                    <thead>
                        <tr>
                            <th>Tên Tour</th>
                            <th>Thời Gian Đặt</th>
                            <th>Số Lượng</th>
                            <th>Giá Mỗi Người</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="detail_mb.php?id=<?= (int)$row['tour_id'] ?>" class="tour-link">
                                        <?php echo htmlspecialchars($row['tour_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row['thoi_gian_dat'])); ?></td>
                                <td><?php echo $row['so_luong']; ?></td>
                                <td><?php echo number_format($row['tour_price'], 0, '', '.'); ?> đồng</td>
                                <td><?php echo number_format($row['total_money'], 0, '', '.'); ?> đồng</td>
                                <td>
                                    <span class="status <?php echo 'status-' . strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $booking_time = strtotime($row['thoi_gian_dat']);
                                    $current_time = time();
                                    $hours_since_booking = ($current_time - $booking_time) / 3600;
                                    
                                    $can_cancel = 
                                        strtolower($row['status']) != 'Đã xác nhận' && strtolower($row['status']) != 'Đã hủy' &&
                                        $hours_since_booking <= 24;
                                    ?>
                                    
                                    <?php if ($can_cancel): ?>
                                        <button onclick="huyTour(<?php echo $row['id_booking_tour']; ?>)" class="btn-huy-tour">
                                            Hủy Tour
                                        </button>
                                    <?php else: ?>
                                        <span class="status-cannot-cancel">
                                            <?php 
                                            if (strtolower($row['status']) == 'Đã xác nhận'  ) {
                                                echo 'Không thể hủy';
                                            }else if(strtolower($row['status']) == 'Đã hủy'){
                                                echo 'Tour đã bị hủy';
                                            } 
                                            else {
                                                echo 'Quá hạn hủy';
                                            }
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="my-tour-no-booking">Chưa có tour nào được đặt.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function huyTour(bookingId) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn muốn hủy tour này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, hủy tour!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('huytour.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'booking_id=' + bookingId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Đã Hủy!',
                                    'Tour của bạn đã được hủy.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            Swal.fire(
                                'Lỗi!',
                                'Đã có lỗi xảy ra.',
                                'error'
                            );
                        });
                }
            });
        }
    </script>
</body>

</html>