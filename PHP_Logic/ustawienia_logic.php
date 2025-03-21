<?php
session_start();
require('database_connection.php');
require('../PHP_Logic/Logi/logMessage.php');
if (isset($_POST['newLogin']) && isset($_POST['currentPassword']) && isset($_POST['newPassword'])) {
    $newLogin = $_POST['newLogin'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $user_id = $_SESSION['user_id'];
    $currentLogin = $_SESSION['login'];

    // Sprawdzenie, czy nowe hasło spełnia wymagania
    if (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword)) {
        echo "<script>alert('Nowe hasło musi mieć co najmniej 8 znaków i zawierać co najmniej jedną cyfrę.'); window.location.href='../sites/ustawienia.php';</script>";
        logMessage("Error","Błedne nowe hasło",$_SESSION['user_id']);
        exit();
    }

    $conn = db_connect();

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
                // Aktualizacja loginu i hasła
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $query = "UPDATE uzytkownicy SET login = '$newLogin', haslo = '$hashedPassword' WHERE id = '$user_id'";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['login'] = $newLogin;
                    echo "<script>alert('Login i hasło zostały pomyślnie zmienione.'); window.location.href='../sites/ustawienia.php';</script>";
                    logMessage("Info","Zmiana loginu i hasła",$_SESSION['user_id']);
                } else {
                    echo "<script>alert('Błąd podczas aktualizacji danych.'); window.location.href='../sites/ustawienia.php';</script>";
                    logMessage("Error","Błąd podczas aktualizacji danych",$_SESSION['user_id']);
                }
            } else {
                echo "<script>alert('Nowy login już istnieje.'); window.location.href='../sites/ustawienia.php';</script>";
                logMessage("Error","Nowy login już istnieje",$_SESSION['user_id']);
            }
        } else {
            echo "<script>alert('Obecne hasło jest nieprawidłowe.'); window.location.href='../sites/ustawienia.php';</script>";
            logMessage("Error","Błedne obecne hasło",$_SESSION['user_id']);
        }
    } else {
        echo "<script>alert('Obecny login jest nieprawidłowy.'); window.location.href='../sites/ustawienia.php';</script>";
        logMessage("Error","Błedny obecny login",$_SESSION['user_id']);
    }

    mysqli_close($conn);
} else {
    echo "<script>alert('Wszystkie pola są wymagane.'); window.location.href='../sites/ustawienia.php';</script>";
    logMessage("Error","Wszystkie pola są wymagane",$_SESSION['user_id']);
}
?>