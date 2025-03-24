<?php
require('database_connection.php');
require_once('../PHP_Logic/Logi/logMessage.php');
session_start();

//sprawdza czy pola sa wypełnione w formularzu 
if (isset($_POST['username']) && isset($_POST['password'])) {
    $login = $_POST['username'];
    $password = $_POST['password'];

    $conn = db_connect(); // laczenie 
    $query = "SELECT id, haslo, adresEmail FROM uzytkownicy WHERE login = '$login'"; // zapytanie do bazy danych
    $result = mysqli_query($conn, $query); // wykonanie zapytania
        //sprawdza czy wynik zapytnai istnieje 
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // pobiera wynik jako pierwszy wiersz tablicy assosciacyjnej
    
        if ($password == $row['haslo'] || password_verify($password, $row['haslo']) ) { 
            $_SESSION['user_id'] = $row['id']; // ustawinie id sesji 
            $_SESSION['login'] = $login; // ustawienie loginu sesji
            logMessage('INFO', 'Zalogowano', $login); // logowanie zdarzenia
            header('Location: ../sites/index.php'); // po porawnym zalogowaniu przekierowuje do strony index.php
            exit();
        } else {
            $_SESSION['error'] = "Nieprawidłowy login lub hasło."; // erro ktory był użyty w wioku jako blad
            logMessage('ERROR', 'Błędne dane logowania', $row['id']); // logowanie zdarzenia
        }
    } else {
        $_SESSION['error'] = "Nieprawidłowy login lub hasło."; // erro ktory był użyty w wioku jako blad 
        logMessage('ERROR', 'Błędne dane logowania', $row['id']);
    }

    mysqli_close($conn); // zamkniecie polaczenia 
    header('Location: ../sites/login.php'); // powrot do strony logowania 
    exit();
}
?>