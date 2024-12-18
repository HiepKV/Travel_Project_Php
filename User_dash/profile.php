<?php
session_start();
include 'connect.php';


$user = null;
if (isset($_SESSION['id'])) {
    $result = $conn->query("SELECT * FROM NguoiDung WHERE id = " . $_SESSION['id']);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
}



?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        form {
            width: 550px;
            height: auto;
            border: 1px solid black;
            margin: 0 auto;
            padding-left: 20px;
            margin-top: 50px;
            border-radius: 10px;
            background-color: rgb(105, 193, 65);


        }

        h2 {
            text-align: center;
        }

        input {
            width: 400px;
            height: 30px;
            margin-top: 10px;
            border-radius: 10px;
        }

        #dattour {
            position: relative;
            left: 420px;
            background-color: red;
            color: #ffff;
        }

        #dattour:hover {
            background-color: rgb(105, 119, 255);
        }
    </style>
</head>

<body>
    <form action="">
        <h2>Thông Tin Của Bạn</h2>
        <table>
            <tr>
                <td>Họ và tên:</td>
                <td><input type="text" value="<?php echo $user['HoVaTen']; ?>"></td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td><input type="text" value="<?php echo $user['SDT']; ?>"></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type="text" value="<?php echo $user['Email']; ?>"></td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td><input type="text" value="<?php echo $user['DiaChi']; ?>"></td>
            </tr>

        </table>


        <input id="dattour" type="button" value="OK" style="width: 70px;" onclick="window.location.href='index.php'">






    </form>



</body>

</html>