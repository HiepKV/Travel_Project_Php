<?php
session_start();
include '../includes/connect.php';
// Ensure case-insensitive comparison and use prepared statement
$query = "SELECT * FROM Tours WHERE LOWER(vung_mien) = LOWER(?) ORDER BY tour_id ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $region);
$region = 'Miền Bắc';
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);

?>
<?php include('../layout/header.php'); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miền Bắc</title>
    <link rel="stylesheet" href="../template/css/mienbac.css">
</head>

<body>
    <div class="tour-container">
        <div class="tour-header">
            <h2>Miền Bắc</h2>
        </div>
        <div class="tour-grid">
            <?php while ($row = mysqli_fetch_assoc($products)) { ?>
                <div class="tour-card">
                    <div class="tour-card-image">
                        <?php
                        $image_path = '../tours/uploads/' . basename($row['img']);
                        ?>
                        <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($row['tour_name']) ?>" title="Tour">
                        <div class="tour-card-badge">
                            <?= htmlspecialchars($row['thoigian']) ?>
                        </div>
                    </div>
                    <div class="tour-card-content">
                        <h3 class="tour-card-title"><?= htmlspecialchars($row['tour_name']) ?></h3>
                        <div class="tour-card-description">
                            <?= htmlspecialchars($row['mota']) ?>
                        </div>
                        <div class="tour-card-details">
                            <div class="tour-detail">
                                <i class="icon-hotel"></i>
                                <p>Khách sạn: <?= htmlspecialchars($row['khachsan'] ?? '5*') ?></p>
                            </div>
                            <div class="tour-detail">
                                <i class="icon-location"></i>
                                <span><?= htmlspecialchars($row['departure_location']) ?></span>
                            </div>
                        </div>
                        <div class="tour-card-footer">
                            <div class="tour-price">
                                <?= number_format($row['price'], 0, ",", ".") ?>đ
                            </div>
                            <a href="../booking/detail_mb.php?id=<?= (int)$row['tour_id'] ?>" class="tour-button">
                                Xem Chi Tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
<?php
include('../layout/footer.php');
?>