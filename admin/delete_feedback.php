<?php
include("../includes/connect.php");

if (isset($_GET['id'])) {
    $id_fb = (int) $_GET['id'];

    // Kiểm tra xem feedback có tồn tại trong cơ sở dữ liệu không
    $check_query = "SELECT * FROM feed_back WHERE id_fb = $id_fb";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Thực hiện xóa feedback
        $delete_query = "DELETE FROM feed_back WHERE id_fb = $id_fb";
        if (mysqli_query($conn, $delete_query)) {
            header('Location: http://localhost/projectphp/admin/dashboard.php?page=reviews');
            exit();
        } else {
            echo "Lỗi khi xóa feedback: " . mysqli_error($conn);
        }
    } else {
        echo "Feedback không tồn tại.";
    }
} else {
    echo "Không có ID feedback để xóa.";
}

mysqli_close($conn);
?>
