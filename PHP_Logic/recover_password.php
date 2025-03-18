<?php
require('database_connection.php');
require '../vendor/autoload.php'; // Adjust the path if necessary
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

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
                // Ustawienia serwera
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Ustaw serwer SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'zastepstwa.przedszkolezielonki@gmail.com'; // Nazwa użytkownika SMTP
                $mail->Password = 'tkxp agdb wqyw czro'; // Hasło SMTP
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Odbiorcy
                $mail->setFrom('no-reply@substitutionplanner.com', 'Zastepstwa Przedszkole Zielonki');
                $mail->addAddress($email);

                // Treść wiadomości
                $mail->isHTML(true);
                $mail->Subject = 'Odzyskiwanie hasła';
                $mail->Body    = "Twoje nowe dane do logowania:<br>Login: $login<br>Hasło: $newPassword";

                $mail->send();
                $_SESSION['error'] = "Nowe dane do logowania zostały wysłane na podany adres email.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Wystąpił błąd podczas wysyłania emaila: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error'] = "Wystąpił błąd podczas aktualizacji hasła.";
        }
    } else {
        $_SESSION['error'] = "Nie znaleziono użytkownika z podanym adresem email.";
    }

    mysqli_close($conn);
    header('Location: ../sites/login.php');
    exit();
}
?>