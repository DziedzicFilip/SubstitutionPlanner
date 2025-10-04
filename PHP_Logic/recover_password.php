<?php

require('database_connection.php');
require '../vendor/autoload.php'; // Dotenv i PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once('../PHP_Logic/Logi/logMessage.php');
session_start();

// Ładowanie .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $conn = db_connect();
    $query = "SELECT id, login FROM uzytkownicy WHERE adresEmail = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $userId = $row['id'];
        $login = $row['login'];

        // Generowanie nowego losowego hasła
        $newPassword = bin2hex(random_bytes(4)); // 8 znaków
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Aktualizacja hasła użytkownika w bazie danych
        $updateQuery = "UPDATE uzytkownicy SET haslo = '$hashedPassword' WHERE id = $userId";
        if (mysqli_query($conn, $updateQuery)) {
            // Wysyłanie emaila z nowymi danymi do logowania za pomocą PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Ustawienia serwera z .env
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $_ENV['MAIL_PORT'];

                // Odbiorcy
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email);

                // Treść wiadomości
                $mail->isHTML(true);
                $mail->Subject = 'Odzyskiwanie hasla';
                $mail->Body    = "Twoje nowe dane do logowania:<br>Login: $login<br>Hasło: $newPassword";

                $mail->send();
                $_SESSION['success'] = "Nowe dane do logowania zostały wysłane na podany adres email.";
                logMessage('INFO', 'Wysłano email z nowym hasłem', $userId);
            } catch (Exception $e) {
                $_SESSION['error'] = "Wystąpił błąd podczas wysyłania emaila: {$mail->ErrorInfo}";
                logMessage('ERROR', 'Błąd podczas wysyłania emaila', $userId);
            }
        } else {
            $_SESSION['error'] = "Wystąpił błąd podczas aktualizacji hasla.";
            logMessage('ERROR', 'Błąd podczas aktualizacji hasla', $userId);
        }
    } else {
        $_SESSION['error'] = "Nie znaleziono użytkownika z podanym adresem email.";
        logMessage('ERROR', 'Nie znaleziono użytkownika z podanym adresem email', $email);
    }

    mysqli_close($conn);
    header('Location: ../sites/login.php');
    exit();
}
?>