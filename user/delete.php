<?php
include '../includes/connect.php';

$UserID = $_GET['id'];

$sql = "DELETE FROM Account WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();

$sql = "DELETE FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();

header("Location: http://localhost/projectphp/admin/dashboard.php?page=user");
exit;
?>
