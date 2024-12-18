<?php
include '../includes/connect.php';
// include('../admin/header.php');
$sql = "
    SELECT 
        Users.UserID, 
        Users.Name, 
        Users.Role, 
        Users.Phone, 
        Users.Address, 
        Account.Email, 
        Account.AccountID
    FROM Users 
    INNER JOIN Account ON Users.UserID = Account.UserID
";
$result = $conn->query($sql);
?>
<?php
include '../includes/connect.php';
$sql = "
    SELECT 
        Users.UserID, 
        Users.Name, 
        Users.Role, 
        Users.Phone, 
        Users.Address, 
        Account.Email, 
        Account.AccountID
    FROM Users 
    INNER JOIN Account ON Users.UserID = Account.UserID
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh sách người dùng và tài khoản</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Danh sách người dùng</h1>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UserID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên người dùng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['UserID']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row['Name']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['Email']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="<?= 
                                        $row['Role'] == 'Admin' ? 'bg-green-100 text-green-800' : 
                                        ($row['Role'] == 'User' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                                    ?> px-2 py-1 rounded-full text-xs font-medium">
                                        <?= htmlspecialchars($row['Role']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['Phone']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['Address']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <button 
                                        onclick="if(confirm('Bạn có chắc muốn xóa?')) location.href='../user/delete.php?id=<?= $row['UserID'] ?>'"
                                        class="text-red-600 hover:text-red-900 mr-3 bg-red-100 px-3 py-1 rounded-md transition duration-300"
                                    >
                                        Xóa
                                    </button>
                                    <button 
                                        onclick="location.href='?page=user&action=role&id=<?= $row['UserID'] ?>'"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 px-3 py-1 rounded-md transition duration-300"
                                    >
                                        Phân quyền
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>