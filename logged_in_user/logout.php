<?php
session_start();
session_unset();
session_destroy();
header("Location: ../User_dash/dashboard.php"); 
exit();
?>
