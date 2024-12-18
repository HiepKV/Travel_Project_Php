<?php
//include 'connect.php';
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/function.php';



class signupTest extends TestCase {
    //test case 1:kiem tra tai khoan ton tai
    public function testCheckTaiKhoanTonTai()
{
    // Kết nối đến cơ sở dữ liệu thực tế
    require_once 'db_config.php';

    // Kiểm tra tài khoản tồn tại
    $taiKhoan = 'chien2003'; // Giả sử tài khoản này có sẵn trong cơ sở dữ liệu
    $result = checkTaiKhoanTonTai($conn, $taiKhoan);
    $this->assertTrue($result, 'Tài khoản không được tìm thấy trong cơ sở dữ liệu.');

    
}

    

//--------------------------------------------------------



    private $conn;

   // Kiểm tra mật khẩu hợp lệ--test case2
public function testPasswordValid() {
    $matKhau = 'Pass1234567';
    $xacnhanmatkhau = 'Pass123456';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('', $result, 'Mật khẩu hợp lệ, không có lỗi.');
}

// Kiểm tra mật khẩu và xác nhận không khớp-test case3
public function testPasswordMismatch() {
    $matKhau = 'Pass123';
    $xacnhanmatkhau = 'Pass124';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('Mật khẩu và xác nhận mật khẩu không khớp.', $result, 'Mật khẩu và xác nhận mật khẩu không khớp.');
}

//Kiểm tra mật khẩu quá ngắn-test case4
public function testPasswordTooShort() {
    $matKhau = 'Pass123';
    $xacnhanmatkhau = 'Pass123';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('Độ dài mật khẩu phải ít nhất 6 ký tự.', $result, 'Mật khẩu phải ít nhất 6 ký tự.');
}

// Kiểm tra mật khẩu thiếu chữ in hoa-test case5
public function testPasswordMissingUppercase() {
    $matKhau = 'pass123';
    $xacnhanmatkhau = 'pass123';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('Yêu cầu mật khẩu của bạn phải có cả chữ in hoa, chữ thường và số!', $result, 'Mật khẩu phải có chữ in hoa, chữ thường và số.');
}

// Kiểm tra mật khẩu thiếu chữ thường-test case6
public function testPasswordMissingLowercase() {
    $matKhau = 'PASS123';
    $xacnhanmatkhau = 'PASS123';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('Yêu cầu mật khẩu của bạn phải có cả chữ in hoa, chữ thường và số!', $result, 'Mật khẩu phải có chữ in hoa, chữ thường và số.');
}

// Kiểm tra mật khẩu thiếu số-test case7
public function testPasswordMissingNumber() {
    $matKhau = 'PassABC';
    $xacnhanmatkhau = 'PassABC';
    $result = validatePassword($matKhau, $xacnhanmatkhau);
    $this->assertEquals('Yêu cầu mật khẩu của bạn phải có cả chữ in hoa, chữ thường và số!', $result, 'Mật khẩu phải có chữ in hoa, chữ thường và số.');
}


//------------------------------------------------------------

// Kiểm tra số điện thoại hợp lệ-test case8
public function testPhoneNumberValid() {
    $sdt = '012345678999'; // Số điện thoại hợp lệ với 10 chữ số
    $result = validatePhoneNumber($sdt);
    $this->assertEquals('', $result, 'Số điện thoại hợp lệ, không có lỗi.');
}

// Kiểm tra số điện thoại quá ngắn-test case9
public function testPhoneNumberTooShort() {
    $sdt = '03561'; // Chỉ có 8 chữ số
    $result = validatePhoneNumber($sdt);
    $this->assertEquals('Định dạng số điện thoại của bạn chưa đúng.', $result, 'Số điện thoại phải có đủ 10 chữ số.');
}

// Kiểm tra số điện thoại quá dài-test case10
public function testPhoneNumberTooLong() {
    $sdt = '012345678901'; // 12 chữ số
    $result = validatePhoneNumber($sdt);
    $this->assertEquals('Định dạng số điện thoại của bạn chưa đúng.', $result, 'Số điện thoại phải có đủ 10 chữ số.');
}

// Kiểm tra số điện thoại chứa ký tự không phải số-test case11
public function testPhoneNumberContainsNonNumericCharacter() {
    $sdt = '01234A6789'; // Chứa ký tự 'A'
    $result = validatePhoneNumber($sdt);
    $this->assertEquals('Định dạng số điện thoại của bạn chưa đúng.', $result, 'Số điện thoại chỉ được chứa các chữ số.');
}

// Kiểm tra số điện thoại chứa ký tự đặc biệt-test case12
public function testPhoneNumberContainsSpecialCharacter() {
    $sdt = '01234#6789'; // Chứa ký tự '#'
    $result = validatePhoneNumber($sdt);
    $this->assertEquals('Định dạng số điện thoại của bạn chưa đúng.', $result, 'Số điện thoại chỉ được chứa các chữ số.');
}
// //----------------------------------------------------------


protected function setUp(): void {
    // Mô phỏng đối tượng mysqli
    $this->conn = $this->createMock(mysqli::class);

    // Mô phỏng phương thức prepare để trả về một đối tượng mysqli_stmt mock
    $stmt = $this->createMock(mysqli_stmt::class);
    $this->conn->method('prepare')->willReturn($stmt);
    
    // Mô phỏng hành động gọi execute
    $stmt->method('execute')->willReturn(true);
}


public function testRegisterUserWithValidData()
{
    $servername = "localhost";
    $username = "root";
    $password = "Dinhchien03*";
    $dbname = "dulich2";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Dữ liệu đầu vào hợp lệ
    $taiKhoan = 'chiensdfhdfhdfgfgfhsrgrt';
$matKhau = 'Abcd1234'; 
$xacNhanMatKhau = 'Abcd1234'; // Xác nhận mật khẩu khớp
$hoVaTen = 'Nguyen Van A';
$sdt = '012345678999';
$email = 'chien2003@example.com';
$diachi = 'Hanoi';
$role = 'user';

// Kiểm tra tài khoản
    if (checkTaiKhoanTonTai($conn, $taiKhoan)) {
        $this->fail('Tài khoản đã tồn tại, hãy nhập tài khoản khác.');
    }

    // Kiểm tra mật khẩu
    if (validatePassword($matKhau, $xacNhanMatKhau)) {
        $this->fail('Mật khẩu không hợp lệ hoặc không khớp.');
    }
    if (validatePhoneNumber($sdt)) {
        $this->fail('Số điện thoại không hợp lệ.');
    }

    // Gọi hàm đăng ký
    $result = registerUser($conn, $taiKhoan, $matKhau, $hoVaTen, $sdt, $email, $diachi, $role);
    $this->assertTrue($result, 'Hàm đăng ký phải trả về true khi thực hiện thành công.');
}
}
?>
