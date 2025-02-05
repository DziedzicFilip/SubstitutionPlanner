<?php
session_start();
session_destroy();
header('Location: ../sites/login.php');
exit();
?>