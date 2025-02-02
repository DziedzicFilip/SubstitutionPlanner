<!-- filepath: /c:/xampp/htdocs/SubstitutionPlanner/SubstitutionPlanner/PHP_Logic/login_logic.php -->
<?php
require('database_connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['username'];
    $password = $_POST['password'];

    $conn = db_connect();

    // Sanitize input
    $login = mysqli_real_escape_string($conn, $login);
    $password = mysqli_real_escape_string($conn, $password);

    // Query to check if the user exists
    $query = "SELECT id, haslo FROM uzytkownicy WHERE login = '$login'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Verify password
        if ($password === $row['haslo']) { // Assuming passwords are stored in plain text
            // Password is correct, set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['login'] = $login;
            header('Location: ../sites/index.html');
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