
<?php
require('database_connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['username'];
    $password = $_POST['password'];

    $conn = db_connect();
    $query = "SELECT id, haslo FROM uzytkownicy WHERE login = '$login'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    
        if ($password === $row['haslo']) { 
        
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['login'] = $login;
            header('Location: ../sites/index.php');
            exit();
        } else {
            $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
        }
    } else {
        $_SESSION['error'] = "Nieprawidłowy login lub hasło.";
    }

    mysqli_close($conn);
    header('Location: ../sites/login.php');
    exit();
}
?>