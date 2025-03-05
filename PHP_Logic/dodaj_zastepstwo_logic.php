<?php
if (session_status() == PHP_SESSION_NONE) { //sprawdzenie czy sesja dziala 
    session_start();
}
require_once('../PHP_Logic/database_connection.php');

function getUserGroups($userId) {
    $conn = db_connect();
    $query = "SELECT Distinct  g.id, g.nazwa 
              FROM pracownik_grupa pg 
              JOIN grupy g ON pg.id_grupy = g.id 
              WHERE pg.id_pracownika = '$userId'";
    $result = mysqli_query($conn, $query);
    $groups = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
    }
    mysqli_close($conn);
    return $groups;
}

if (isset($_POST['date']) && isset($_POST['start_time']) && isset($_POST['end_time'])  &&  isset($_POST['group'])) {
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $requestingEmployeeId = $_SESSION['user_id'];
    $group = $_POST['group'];

    $conn = db_connect();

    $query = "INSERT INTO zastepstwa (data_zastepstwa, godzina_od, godzina_do, id_pracownika_proszacego, status,nazwa_grupy) 
              VALUES ('$date', '$startTime', '$endTime', '$requestingEmployeeId', 'oczekujące','$group')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../sites/dodaj_zastepstwo.php");
        exit();
    } else {
        echo "Błąd: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>