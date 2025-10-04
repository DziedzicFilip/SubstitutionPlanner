<?php
session_start();
require('database_connection.php');
require_once('../PHP_Logic/Logi/logMessage.php');

$user_id = $_SESSION['user_id'];
$currentLogin = $_SESSION['login'];
$conn = db_connect();

if (isset($_POST['changeLogin'])) {
    // Zmiana loginu
    $newLogin = $_POST['newLogin'];
    $currentPassword = $_POST['currentPasswordLogin'];

    // Sprawdzenie obecnego loginu i hasła
    $query = "SELECT haslo FROM uzytkownicy WHERE id = '$user_id' AND login = '$currentLogin'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($currentPassword, $row['haslo']) || $currentPassword === $row['haslo']) {
            // Sprawdzenie, czy nowy login już istnieje
            $query = "SELECT id FROM uzytkownicy WHERE login = '$newLogin'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) == 0) {
                // Aktualizacja loginu
                $query = "UPDATE uzytkownicy SET login = '$newLogin' WHERE id = '$user_id'";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['login'] = $newLogin;
                    echo "<script>alert('Login został pomyślnie zmieniony.'); window.location.href='../sites/ustawienia.php';</script>";
                    logMessage("Info","Zmiana loginu",$_SESSION['user_id']);
                } else {
                    echo "<script>alert('Błąd podczas aktualizacji loginu.'); window.location.href='../sites/ustawienia.php';</script>";
                    logMessage("Error","Błąd podczas aktualizacji loginu",$_SESSION['user_id']);
                }
            } else {
                echo "<script>alert('Nowy login już istnieje.'); window.location.href='../sites/ustawienia.php';</script>";
                logMessage("Error","Nowy login już istnieje",$_SESSION['user_id']);
            }
        } else {
            echo "<script>alert('Obecne hasło jest nieprawidłowe.'); window.location.href='../sites/ustawienia.php';</script>";
            logMessage("Error","Błędne obecne hasło przy zmianie loginu",$_SESSION['user_id']);
        }
    } else {
        echo "<script>alert('Obecny login jest nieprawidłowy.'); window.location.href='../sites/ustawienia.php';</script>";
        logMessage("Error","Błędny obecny login przy zmianie loginu",$_SESSION['user_id']);
    }
} elseif (isset($_POST['changePassword'])) {
    // Zmiana hasła
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    // Sprawdzenie, czy nowe hasło spełnia wymagania
    if (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword)) {
        echo "<script>alert('Nowe hasło musi mieć co najmniej 8 znaków i zawierać co najmniej jedną cyfrę.'); window.location.href='../sites/ustawienia.php';</script>";
        logMessage("Error","Błędne nowe hasło",$_SESSION['user_id']);
        exit();
    }

    // Sprawdzenie obecnego loginu i hasła
    $query = "SELECT haslo FROM uzytkownicy WHERE id = '$user_id' AND login = '$currentLogin'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($currentPassword, $row['haslo']) || $currentPassword === $row['haslo']) {
            // Aktualizacja hasła
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE uzytkownicy SET haslo = '$hashedPassword' WHERE id = '$user_id'";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Hasło zostało pomyślnie zmienione.'); window.location.href='../sites/ustawienia.php';</script>";
                logMessage("Info","Zmiana hasła",$_SESSION['user_id']);
            } else {
                echo "<script>alert('Błąd podczas aktualizacji hasła.'); window.location.href='../sites/ustawienia.php';</script>";
                logMessage("Error","Błąd podczas aktualizacji hasła",$_SESSION['user_id']);
            }
        } else {
            echo "<script>alert('Obecne hasło jest nieprawidłowe.'); window.location.href='../sites/ustawienia.php';</script>";
            logMessage("Error","Błędne obecne hasło przy zmianie hasła",$_SESSION['user_id']);
        }
    } else {
        echo "<script>alert('Obecny login jest nieprawidłowy.'); window.location.href='../sites/ustawienia.php';</script>";
        logMessage("Error","Błędny obecny login przy zmianie hasła",$_SESSION['user_id']);
    }
} else {
    echo "<script>alert('Wszystkie pola są wymagane.'); window.location.href='../sites/ustawienia.php';</script>";
    logMessage("Error","Wszystkie pola są wymagane",$_SESSION['user_id']);
}

mysqli_close($conn);
?>