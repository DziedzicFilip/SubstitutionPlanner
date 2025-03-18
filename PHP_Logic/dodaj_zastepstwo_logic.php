<?php
if (session_status() == PHP_SESSION_NONE) { //sprawdzenie czy sesja dziala 
    session_start();
}
require_once('../PHP_Logic/database_connection.php');

function isAdminD() {
    return isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin';
}

function getUserGroups($userId) {
    $conn = db_connect();
    $query = "SELECT DISTINCT g.id, g.nazwa 
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

if (isset($_POST['date']) && isset($_POST['start_time']) && isset($_POST['end_time']) && isset($_POST['group']) ) {
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $requestingEmployeeId = isAdminD() ? $_POST['employee_id'] : $_SESSION['user_id'];
    $group = $_POST['group'];


    $conn = db_connect();

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "INSERT INTO zastepstwa (data_zastepstwa, godzina_od, godzina_do, id_pracownika_proszacego, status, nazwa_grupy) 
              VALUES ('$date', '$startTime', '$endTime', '$requestingEmployeeId', 'oczekujące', '$group')";

    if (mysqli_query($conn, $query)) {
        $id_zastepstwa = mysqli_insert_id($conn);

        foreach ($uzytkownicy as $id_uzytkownika) {
            $query = "INSERT INTO zastepstwa_uzytkownicy (id_zastepstwa, id_uzytkownika) VALUES ('$id_zastepstwa', '$id_uzytkownika')";
            mysqli_query($conn, $query);
        }

        header("Location: ../sites/dodaj_zastepstwo.php");
        exit();
    } else {
        echo "Błąd: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Required fields are missing.";
}
?>