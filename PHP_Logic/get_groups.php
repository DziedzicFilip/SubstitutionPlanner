<?php
require_once('database_connection.php');

if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];
    $conn = db_connect();
    $query = "SELECT DISTINCT g.id, g.nazwa 
              FROM pracownik_grupa pg 
              JOIN grupy g ON pg.id_grupy = g.id 
              WHERE pg.id_pracownika = '$employeeId'";
    $result = mysqli_query($conn, $query);
    $groups = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
    }
    mysqli_close($conn);
    echo json_encode($groups);
}
?>