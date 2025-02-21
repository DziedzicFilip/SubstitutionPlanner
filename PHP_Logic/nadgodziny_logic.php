<?php
require_once('database_connection.php');
require('fpdf.php');
function getOvertimeData($searchTerm = '', $startDate = '', $endDate = '') { // pobieranie danych o nadgodzinach (wyszukiwanie)
    $conn = db_connect(); // laczenie 
    $user_id = $_SESSION['user_id'];
    $isAdmin = $_SESSION['rola'] === 'admin';

    $query = "SELECT u.id, CONCAT(u.imie, ' ', u.nazwisko) AS full_name, SUM(n.liczba_godzin) AS total_hours
              FROM nadgodziny n
              JOIN uzytkownicy u ON n.id_pracownika = u.id"; // baza zapytania 
    $conditions = []; // tablica warunkow 
    if (!empty($searchTerm)) { // jesli nie puste dodaje do tablicy 
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
        $query .= " WHERE " . implode(' AND ', $conditions); // łącznie warunków implode laczy wartosc w tablicy w jedna calosc
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

function getOvertimeDetails($userId, $startDate = '', $endDate = '') { // pobieranie  ilosci godzin 
    $conn = db_connect(); // laczenie 
    $query = "SELECT data, liczba_godzin FROM nadgodziny WHERE id_pracownika = '$userId'"; // zapytnaie
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
            echo '<div class="col-md-4 mb-4 user-overtime" data-username="' . strtolower($user['full_name']) . '"> 
            <div class="card"> 
            <div class="card-body">';
            echo '<h5 class="card-title">' . $user['full_name'] . '</h5>';
            echo '<p class="card-text">Całkowite nadgodziny: ' . $user['total_hours'] . '</p>';
            echo '<div class="table-responsive"> <table class="table schedule-table mt-4">
            <thead><tr>
            <th>Data</th>
            <th>Liczba godzin</th>
            </tr>
            </thead> <tbody>';
            $overtimeDetails = getOvertimeDetails($user['id'], $startDate, $endDate);
            foreach ($overtimeDetails as $detail) {
                echo '<tr>  <td>' . $detail['data'] . '</td>';
                echo '<td>' . $detail['liczba_godzin'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>
            </table>
            </div></div></div></div>';
        }
    } else {
        echo '<p class="text-center">Brak danych o nadgodzinach.</p>';
    }
}

function generatePDF($searchTerm = '', $startDate = '', $endDate = '') {
    $overtimeData = getOvertimeData($searchTerm, $startDate, $endDate);
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Raport Nadgodzin', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    if (!empty($overtimeData)) {
        foreach ($overtimeData as $user) {
            $pdf->Cell(0, 10, 'Pracownik: ' . $user['full_name'], 0, 1);
            $pdf->Cell(0, 10, 'Liczba nadgodzin: ' . $user['total_hours'], 0, 1);
            $pdf->Ln(5);
            $pdf->Cell(40, 10, 'Data', 1);
            $pdf->Cell(40, 10, 'Godziny', 1);
            $pdf->Ln();

            $overtimeDetails = getOvertimeDetails($user['id'], $startDate, $endDate);
            foreach ($overtimeDetails as $detail) {
                $pdf->Cell(40, 10, $detail['data'], 1);
                $pdf->Cell(40, 10, $detail['liczba_godzin'], 1);
                $pdf->Ln();
            }
            $pdf->Ln(10);
        }
    } else {
        $pdf->Cell(0, 10, 'Brak Nadgodzin.', 0, 1, 'C');
    }

    $pdf->Output('D', 'Nadgodziny.pdf');
}

$searchTerm = isset($_GET['searchUser']) ? $_GET['searchUser'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

if (isset($_GET['generatePDF'])) {
    generatePDF($searchTerm, $startDate, $endDate);
} 
?>