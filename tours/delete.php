<?php
include 'C:\xampp\htdocs\projectphp\includes\connect.php'; // Kết nối đến database


if (isset($_GET["id"])) {
    $tour_id = $_GET["id"];
    $sql = "DELETE FROM tours WHERE tour_id = $tour_id";

    if ($conn->query($sql) === TRUE) {
        
        header("Location: http://localhost/projectphp/admin/dashboard.php?page=tours");
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
} else {
    echo "Không có TourID để xóa.";
}

$conn->close();
?>
