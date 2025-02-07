<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../PHP_Logic/database_connection.php');


if (isset($_POST['date']) && isset($_POST['start_time']) && isset($_POST['end_time'])) {
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $requestingEmployeeId = $_SESSION['user_id'];

    $conn = db_connect();

    $query = "INSERT INTO zastepstwa (data_zastepstwa, godzina_od, godzina_do, id_pracownika_proszacego, status) 
              VALUES ('$date', '$startTime', '$endTime', '$requestingEmployeeId', 'oczekujące')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../sites/dodaj_zastepstwo.php");
        exit();
    } else {
        echo "Błąd: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>