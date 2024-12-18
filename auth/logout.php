<?php
// Bắt đầu session
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/projectphp/includes/connect.php';

session_unset(); 
session_destroy(); 
header("Location: http://localhost/projectphp/auth/login.php");
exit;
?>
