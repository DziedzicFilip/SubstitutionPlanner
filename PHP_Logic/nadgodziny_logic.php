<?php
require_once('database_connection.php');

function getOvertimeData($searchTerm = '', $startDate = '', $endDate = '') {
    $conn = db_connect();
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $user_id = $_SESSION['user_id'];
    $isAdmin = $_SESSION['rola'] === 'admin';

    $query = "SELECT u.id, CONCAT(u.imie, ' ', u.nazwisko) AS full_name, SUM(n.liczba_godzin) AS total_hours
              FROM nadgodziny n
              JOIN uzytkownicy u ON n.id_pracownika = u.id";
    $conditions = [];
    if (!empty($searchTerm)) {
        $conditions[] = "CONCAT(u.imie, ' ', u.nazwisko) LIKE '%$searchTerm%'";
    }
    if (!empty($startDate)) {
        $conditions[] = "n.data >= '$startDate'";
    }
    if (!empty($endDate)) {
        $conditions[] = "n.data <= '$endDate'";
    }
    if (!$isAdmin) {
        $conditions[] = "n.id_pracownika = '$user_id'";
    }
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
    $query .= " GROUP BY u.id";
    $result = mysqli_query($conn, $query);
    $overtimeData = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $overtimeData[] = $row;
        }
    }
    mysqli_close($conn);
    return $overtimeData;
}

function getOvertimeDetails($userId, $startDate = '', $endDate = '') {
    $conn = db_connect();
    $query = "SELECT data, liczba_godzin FROM nadgodziny WHERE id_pracownika = '$userId'";
    if (!empty($startDate)) {
        $query .= " AND data >= '$startDate'";
    }
    if (!empty($endDate)) {
        $query .= " AND data <= '$endDate'";
    }
    $result = mysqli_query($conn, $query);
    $overtimeDetails = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $overtimeDetails[] = $row;
        }
    }
    mysqli_close($conn);
    return $overtimeDetails;
}

function displayOvertimeCards($searchTerm = '', $startDate = '', $endDate = '') {
    $overtimeData = getOvertimeData($searchTerm, $startDate, $endDate);
    if (!empty($overtimeData)) {
        foreach ($overtimeData as $user) {
            echo '<div class="col-md-4 mb-4 user-overtime" data-username="' . strtolower($user['full_name']) . '">';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $user['full_name'] . '</h5>';
            echo '<p class="card-text">Całkowite nadgodziny: ' . $user['total_hours'] . '</p>';
            echo '<div class="table-responsive">';
            echo '<table class="table schedule-table mt-4">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Data</th>';
            echo '<th>Liczba godzin</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            $overtimeDetails = getOvertimeDetails($user['id'], $startDate, $endDate);
            foreach ($overtimeDetails as $detail) {
                echo '<tr>';
                echo '<td>' . $detail['data'] . '</td>';
                echo '<td>' . $detail['liczba_godzin'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">Brak danych o nadgodzinach.</p>';
    }
}

$searchTerm = isset($_GET['searchUser']) ? $_GET['searchUser'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
?>