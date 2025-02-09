<?php
require_once('../PHP_Logic/database_connection.php');

function getPendingSubstitutions() { // pobiera z bazy danych zastepstwa 
    $conn = db_connect();
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
                     u1.id AS id_pracownika_proszacego, u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              LEFT JOIN pracownik_grupa pg ON u1.id = pg.id_pracownika
              LEFT JOIN grupy g ON pg.id_grupy = g.id
              WHERE z.status = 'oczekujące' ";
    $result = mysqli_query($conn, $query);
    $pendingSubstitutions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pendingSubstitutions[] = $row;
        }
    }
    mysqli_close($conn);
    return $pendingSubstitutions;
}

function getAvailableEmployees($date, $startTime, $endTime, $requestingEmployeeId) { // pokazuje liste pracownikow ktorzy moga zastapic 
    $conn = db_connect(); 

    $daysOfWeek = [ // tablica do dni tygodnia 
        'Monday' => 'Poniedziałek',
        'Tuesday' => 'Wtorek',
        'Wednesday' => 'Środa',
        'Thursday' => 'Czwartek',
        'Friday' => 'Piątek',
        'Saturday' => 'Sobota',
        'Sunday' => 'Niedziela'
    ];
    $dayOfWeek = $daysOfWeek[date('l', strtotime($date))]; //  przyspisanie odpowiedenje wartosci do dnia tygodnia zwraca pelna znawe 
    // $query = "SELECT u.id, u.imie, u.nazwisko
    // FROM uzytkownicy u
    // WHERE u.id != $requestingEmployeeId
    // AND u.id NOT IN (
    //     SELECT h.id_pracownika 
    //     FROM harmonogram h 
    //     WHERE h.dzien = '$dayOfWeek'
    //     AND NOT ('$endTime' <= h.godzina_od OR '$startTime' >= h.godzina_do)
    // ) AND rola = 'pracownik'";
    $query = "SELECT u.id, u.imie, u.nazwisko
              FROM uzytkownicy u
              WHERE  u.id NOT IN (
                  SELECT h.id_pracownika 
                  FROM harmonogram h 
                  WHERE h.dzien = '$dayOfWeek'
                  AND NOT ('$endTime' <= h.godzina_od OR '$startTime' >= h.godzina_do)
              ) AND rola = 'pracownik'"; // zapytanie 

    $result = mysqli_query($conn, $query);
    $availableEmployees = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $availableEmployees[] = $row;
        }
    }

    mysqli_close($conn);
    return $availableEmployees;
}

function displayPendingSubstitutions() {
    $pendingSubstitutions = getPendingSubstitutions();
    echo '<form method="POST" action="ZarzadzajZastepstwami.php">
    <table class="table table-striped">
    <thead>
    <tr><th>Data</th>
    <th>Godzina Od</th>
    <th>Godzina Do</th>
    <th>Potrzebujący</th> 
    <th>Możliwe Zastępstwa</th></tr>
    </thead> <tbody>';
    foreach ($pendingSubstitutions as $substitution) { // iteracja po wszytkich potrzebujacych 
        echo '<tr> <td>' . $substitution['data'] . '</td>';
        echo '<td>' . $substitution['godzina_od'] . '</td>';
        echo '<td>' . $substitution['godzina_do'] . '</td>';
        echo '<td>' . $substitution['imie_potrzebujacego'] . ' ' . $substitution['nazwisko_potrzebujacego'] . '</td> <td>';
        //załadowanie dostepnych pracownikow 
        $availableEmployees = getAvailableEmployees($substitution['data'], $substitution['godzina_od'], $substitution['godzina_do'], $substitution['id_pracownika_proszacego']); 
        if (!empty($availableEmployees)) { // sprawdzenie czy ktos jest  
            foreach ($availableEmployees as $employee) {
                echo '<div class="form-check"> 
                <input class="form-check-input" type="radio" name="selected_employee[' . $substitution['id'] . ']" 
                value="' . $employee['id'] . '" id="employee_' . $employee['id'] . '">';
                echo '<label class="form-check-label" for="employee_' . $employee['id'] . '">' . $employee['imie'] . ' 
                ' . $employee['nazwisko'] . '</label> </div>';
            }
        } else {
            echo 'Brak dostępnych pracowników';
        }
        echo '<button type="submit" name="action" value="accept" class="btn btn-primary">Wyślij</button> 
        <button type="submit" name="action" value="reject" class="btn btn-danger">Odrzuć</button> </td> </tr>';
        }
        echo '</tbody>
        </table></form>';
        }

        function ChangeStatus() {
            $conn = db_connect();
            if (isset($_POST['selected_employee']) && $_POST['action'] === 'accept') { // sprawdzei czy wybrany i akcept 
                foreach ($_POST['selected_employee'] as $substitutionId => $employeeId) { // iteracja po tablicy 
                    // zmiana statusu i ustawienie pracownika zastepujacego
                    $query = "UPDATE zastepstwa SET id_pracownika_zastepujacego = $employeeId, status = 'DoAkceptacji' WHERE id = $substitutionId"; 
                    if (!mysqli_query($conn, $query)) {
                        echo "Error: " . mysqli_error($conn);
                    }
                }
            } elseif ($_POST['action'] === 'reject') { // jesli odrzcuone zmiana statusu na odrzucone
                foreach ($_POST['selected_employee'] as $substitutionId => $employeeId) {
                    $query = "UPDATE zastepstwa SET status = 'odrzucone' WHERE id = $substitutionId";
                    if (!mysqli_query($conn, $query)) {
                        echo "Error: " . mysqli_error($conn);
                    }
                }
            }
            mysqli_close($conn);
            header('Location: ZarzadzajZastepstwami.php'); 
            exit();
        }
        
        if (isset($_POST['selected_employee'])) {
            ChangeStatus();
        }

?>