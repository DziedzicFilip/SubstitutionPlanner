<?php
session_start();
session_destroy(); // niszczenie sesji 
header('Location: ../sites/login.php'); // przekierowanie 
exit();
?>