<?php
session_start();
session_destroy(); 
header("Location: login.php"); // page after confirming logout
exit();
?>
