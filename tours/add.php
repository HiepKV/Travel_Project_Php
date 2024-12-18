<?php
ob_start();
include '../includes/connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tour_name'], $_POST['departure_location'], $_POST['description'], $_POST['price'], $_POST['vung_mien'])) {
        // Lấy dữ liệu từ form
        $tourName = $conn->real_escape_string($_POST['tour_name']);
        $departureLocation = $conn->real_escape_string($_POST['departure_location']);
        $description = $_POST['description'];
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
        // lưu ảnh vào upload
        $imageURL = '';
        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            // Đặt thư mục upload cố định với đường dẫn đầy đủ
            $baseDir = 'C:/xampp/htdocs/projectphp/tours/uploads/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0777, true);
            }

            // Tạo tên file duy nhất
            $imageName = uniqid('tour_') . '_' . basename($_FILES['img']['name']);
            $imagePath = $baseDir . $imageName;

            // Kiểm tra và xử lý upload
            if (move_uploaded_file($_FILES['img']['tmp_name'], $imagePath)) {
                $imageURL = 'tours/uploads/' . $imageName;
            } else {
                echo "Lỗi khi tải ảnh lên.";
                exit;
            }
        } else {
            echo "Lỗi trong quá trình upload file: " . $_FILES['img']['error'];
            exit;
        }

        $sql = "INSERT INTO Tours (
            tour_name, 
            img, 
            mota, 
            price, 
            thoigian, 
            transport, 
            khachsan, 
            departure_location, 
            vung_mien, 
            description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Lỗi chuẩn bị truy vấn: " . $conn->error;
            exit;
        }
        $stmt->bind_param(
            "sssdssssss",
            $tourName,
            $imageURL,
            $mota,
            $price,
            $thoigian,
            $transport,
            $khachsan,
            $departureLocation,
            $vungMien,
            $description
        );
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Tour đã được thêm thành công!";
            echo '<script type="text/javascript">
            alert("Tour đã được thêm thành công!");
            window.location.href = "http://localhost/projectphp/admin/dashboard.php?page=tours";
          </script>';
    exit;
        } else {
            echo "Lỗi khi thêm tour: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Vui lòng điền đầy đủ thông tin!";
    }
}
$conn->close();
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thêm Tour Mới</title>
    <link rel="stylesheet" href="../template/css/add_tour.css">
</head>

<body>
    <header>
        <h1>Thêm Tour Mới</h1>
        <!-- <a href="tour.php">Trở lại Danh sách Tour</a> -->
    </header>

    <main>
        <form id="addTourForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
            <label for="tour_name">Tên Tour:</label>
            <input type="text" id="tour_name" name="tour_name" required>

            <label for="mota">Mô Tả Ngắn:</label>
            <input type="text" id="mota" name="mota" required>

            <label for="img">Ảnh Tour:</label>
            <input type="file" id="img" name="img" required>

            <label for="price">Giá Tour:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="thoigian">Thời Gian:</label>
            <input type="text" id="thoigian" name="thoigian" required>

            <label for="transport">Phương Tiện:</label>
            <input type="text" id="transport" name="transport" required>

            <label for="khachsan">Khách Sạn:</label>
            <input type="text" id="khachsan" name="khachsan">

            <label for="departure_location">Địa Điểm Khởi Hành:</label>
            <input id="departure_location" name="departure_location" required>

            <label for="vung_mien">Vùng Miền:</label>
            <select id="vung_mien" name="vung_mien" required>
                <option value="">Chọn Vùng Miền</option>
                <option value="Miền Bắc">Miền Bắc</option>
                <option value="Miền Trung">Miền Trung</option>
                <option value="Miền Nam">Miền Nam</option>
            </select>

            <label for="description">Mô Tả Chi Tiết:</label>
            <textarea id="description" name="description" rows="6"></textarea>

            <button type="submit" name="submit" id="submit">Thêm Tour</button>
        </form>
        <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
        <script>
            let editorInstance;

            // Khởi tạo CKEditor và lưu instance vào biến
            ClassicEditor
                .create(document.querySelector('#description')), {
                    toolbar: [
                        'undo', 'redo', 'bold', 'italic', 'link',
                        'bulletedList', 'numberedList',
                        'alignment', // Thêm công cụ căn chỉnh
                        'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed'
                    ],
                    alignment: {    
                        options: ['left', 'center', 'right', 'justify'] // Cung cấp các tùy chọn căn chỉnh
                    },
                    image: {
                        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
                    },
                    extraPlugins: [MyCustomUploadAdapterPlugin], // Đăng ký custom adapter
                }


                .then(editor => {
                    editorInstance = editor;
                })
                .catch(error => {
                    console.error('Lỗi khi tải CKEditor:', error);
                    alert("Đã xảy ra lỗi khi tải CKEditor. Vui lòng thử lại hoặc kiểm tra kết nối.");
                });

            function validateForm() {
                const descriptionContent = editorInstance.getData();

                if (descriptionContent.trim() === '') {
                    alert("Vui lòng nhập mô tả cho tour.");
                    return false; 
                }

                document.querySelector('#description').value = descriptionContent;
                return true;
            }
        </script>
    </main>
</body>

</html>