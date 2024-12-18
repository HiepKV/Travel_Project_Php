<?php
include 'C:\xampp\htdocs\projectphp\includes\connect.php'; // Kết nối đến database
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tourID = $_GET['id'];

    // Truy vấn lấy thông tin tour hiện tại
    $sql = "SELECT * FROM Tours WHERE tour_id = $tourID";
    $result = $conn->query($sql);

    // Kiểm tra nếu tour tồn tại trong cơ sở dữ liệu
    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();
    } else {
        echo "Tour không tồn tại.";
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Thu thập dữ liệu từ form
        $tourName = $conn->real_escape_string($_POST['tour_name']);
        $departureLocation = $conn->real_escape_string($_POST['departure_location']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $mota = $conn->real_escape_string($_POST['mota']);
        $thoigian = $conn->real_escape_string($_POST['thoigian']);
        $transport = $conn->real_escape_string($_POST['transport']);
        $khachsan = $conn->real_escape_string($_POST['khachsan']);

        $vungMien = $conn->real_escape_string($_POST['vung_mien']);
        $allowedVungMien = ['Miền Bắc', 'Miền Trung', 'Miền Nam'];
        if (!in_array($vungMien, $allowedVungMien)) {
            echo "Vùng miền không hợp lệ.";
            exit;
        }

        $uploadDir = 'C:/xampp/htdocs/projectphp/tours/uploads/';
        $webPath = 'uploads/';
        $imagePath = $tour['img'];

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $imageName = $_FILES['img']['name'];
            $imageTmpName = $_FILES['img']['tmp_name'];
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));


            $newImageName = uniqid('tour_') . '.' . $imageExt;
            $serverPath = $uploadDir . $newImageName;


            $webImagePath = $webPath . $newImageName;

            // Di chuyển file ảnh vào thư mục upload
            if (move_uploaded_file($imageTmpName, $serverPath)) {
                // Nếu ảnh mới được tải lên, cập nhật đường dẫn mới
                $imagePath = $webImagePath;
            } else {
                echo "Lỗi khi tải ảnh lên.";
                exit;
            }
        }


        // Truy vấn cập nhật thông tin tour
        $updateSQL = "UPDATE Tours 
                      SET tour_name = '$tourName', 
                          img = '$imagePath', 
                          mota = '$mota', 
                          price = $price, 
                          thoigian = '$thoigian', 
                          transport = '$transport', 
                          khachsan = '$khachsan', 
                          departure_location = '$departureLocation', 
                          vung_mien = '$vungMien', 
                          description = '$description'
                      WHERE tour_id = $tourID";

        if ($conn->query($updateSQL) === TRUE) {
            $_SESSION['success_message'] = "Thông tin tour đã được cập nhật thành công!";
            echo "<script>window.location.href = 'http://localhost/projectphp/admin/dashboard.php?page=tours';</script>";
            exit;
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
} else {
    echo "ID tour không hợp lệ hoặc không được cung cấp.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Tour</title>
    <link rel="stylesheet" href="../template/css/edit_tour.css">
</head>

<div class="edit">
    <header>
        <h1>Sửa Thông Tin Tour</h1>
        <a href="http://localhost/projectphp/admin/dashboard.php?page=tours">Trở lại Danh sách Tour</a>
    </header>

    <main>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="tour_name">Tên Tour:</label>
            <input type="text" id="tour_name" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>" required>

            <label for="mota">Mô Tả Ngắn:</label>
            <input type="text" id="mota" name="mota" value="<?php echo htmlspecialchars($tour['mota']); ?>" required>

            <label for="img">Ảnh Tour:</label>

            <?php
            $imagePath = $tour['img'];
            if (!empty($imagePath)) {
                $imagePath = str_replace('tours/uploads/', '', $imagePath);

                $relativeImagePath = '/projectphp/tours/' . $imagePath;

                $fullServerPath = 'C:/xampp/htdocs' . $relativeImagePath;

                if (file_exists($fullServerPath)) {
                    echo "<img src='" . htmlspecialchars($relativeImagePath) . "' alt='Ảnh Tour' style='max-width: 200px; max-height: 200px; margin-bottom: 10px;'>";
                } else {
                    echo "<p>Không tìm thấy ảnh: " . htmlspecialchars($fullServerPath) . "</p>";
                }
            } else {
                echo "<p>Chưa có ảnh cho tour này.</p>";
            }
            ?>
            <input type="file" id="img" name="img">

            <label for="price">Giá Tour:</label>
            <input type="number" id="price" name="price" value="<?php echo number_format($tour['price'], 2, '.', ''); ?>" step="0.01" required>

            <label for="thoigian">Thời Gian:</label>
            <input type="text" id="thoigian" name="thoigian" value="<?php echo htmlspecialchars($tour['thoigian']); ?>" required>

            <label for="transport">Phương Tiện:</label>
            <input type="text" id="transport" name="transport" value="<?php echo htmlspecialchars($tour['transport']); ?>" required>

            <label for="khachsan">Khách Sạn:</label>
            <input type="text" id="khachsan" name="khachsan" value="<?php echo htmlspecialchars($tour['khachsan']); ?>">

            <label for="departure_location">Địa Điểm Khởi Hành:</label>
            <input type="text" id="departure_location" name="departure_location" value="<?php echo htmlspecialchars($tour['departure_location']); ?>" required>

            <label for="vung_mien">Vùng Miền:</label>
            <select id="vung_mien" name="vung_mien" required>
                <option value="Miền Bắc" <?php echo ($tour['vung_mien'] == 'Miền Bắc') ? 'selected' : ''; ?>>Miền Bắc</option>
                <option value="Miền Trung" <?php echo ($tour['vung_mien'] == 'Miền Trung') ? 'selected' : ''; ?>>Miền Trung</option>
                <option value="Miền Nam" <?php echo ($tour['vung_mien'] == 'Miền Nam') ? 'selected' : ''; ?>>Miền Nam</option>
            </select>

            <label for="description">Mô Tả Chi Tiết:</label>
            <!-- Gán nội dung description từ database -->
            <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($tour['description']); ?></textarea>
            <button type="submit">Cập Nhật Tour</button>
        </form>

        <!-- CKEditor cho mô tả chi tiết -->
        <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
        <script>
            let editorInstance;

            // Khởi tạo CKEditor và lưu instance vào biến
            ClassicEditor
                .create(document.querySelector('#description'))
                .then(editor => {
                    editorInstance = editor;
                })
                .catch(error => {
                    console.error('Lỗi khi tải CKEditor:', error);
                    alert("Đã xảy ra lỗi khi tải CKEditor. Vui lòng thử lại hoặc kiểm tra kết nối.");
                });

            // Kiểm tra nội dung CKEditor trước khi submit
            function validateForm() {
                const descriptionContent = editorInstance.getData();

                if (descriptionContent.trim() === '') {
                    alert("Vui lòng nhập mô tả cho tour.");
                    return false; // Ngăn form submit nếu mô tả trống
                }

                // Gán nội dung CKEditor vào textarea để gửi lên server
                document.querySelector('#description').value = descriptionContent;
                return true;
            }
        </script>
    </main>
</div>

</html>