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

if (isset($_POST['date']) && isset($_POST['start_time']) && isset($_POST['end_time'])  &&  isset($_POST['group']) /*&& isset($_POST['uzytkownicy'])*/) {
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $requestingEmployeeId = $_SESSION['user_id'];
    $group = $_POST['group'];
    //$uzytkownicy = $_POST['uzytkownicy'];

    // Debugging: Sprawdź, czy dane są poprawnie przekazywane
    echo "Date: $date<br>";
    echo "Start Time: $startTime<br>";
    echo "End Time: $endTime<br>";
    echo "Requesting Employee ID: $requestingEmployeeId<br>";
    echo "Group: $group<br>";
    echo "Users: ";
    var_dump($uzytkownicy);
    echo "<br>";

    $conn = db_connect();

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "INSERT INTO zastepstwa (data_zastepstwa, godzina_od, godzina_do, id_pracownika_proszacego, status, nazwa_grupy) 
              VALUES ('$date', '$startTime', '$endTime', '$requestingEmployeeId', 'oczekujące', '$group')";

    // // Debugging: Wyświetl zapytanie SQL
    // echo "Query: $query<br>";

    // if (mysqli_query($conn, $query)) {
    //     $id_zastepstwa = mysqli_insert_id($conn);

    //     foreach ($uzytkownicy as $id_uzytkownika) {
    //         $query = "INSERT INTO zastepstwa_uzytkownicy (id_zastepstwa, id_uzytkownika) VALUES ('$id_zastepstwa', '$id_uzytkownika')";
    //         // Debugging: Wyświetl zapytanie SQL
    //         echo "Query: $query<br>";
    //         mysqli_query($conn, $query);
    //     }

    //     header("Location: ../sites/dodaj_zastepstwo.php");
    //     exit();
    // } else {
    //     echo "Błąd: " . mysqli_error($conn);
    // }

    mysqli_close($conn);
} else {
    echo "Required fields are missing.";
}
?>