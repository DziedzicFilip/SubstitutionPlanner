<?php
require_once('../PHP_Logic/database_connection.php');

function getSchedule($startDate = null) {
    $conn = db_connect();
    if ($startDate === null) {
        $startDate = date('Y-m-d');
    }
    $query = "SELECT g.nazwa AS grupa, u.imie, u.nazwisko, h.dzien, h.godzina_od, h.godzina_do
              FROM harmonogram h
              JOIN uzytkownicy u ON h.id_pracownika = u.id
              JOIN pracownik_grupa pg ON u.id = pg.id_pracownika
              JOIN grupy g ON pg.id_grupy = g.id
              ORDER BY g.nazwa, FIELD(h.dzien, 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'), h.godzina_od";
    $result = mysqli_query($conn, $query);
    $schedule = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $schedule[] = $row;
        }
    }
    mysqli_close($conn);
    return $schedule;
}

function getSubstitutions() {
    $conn = db_connect();
    $query = "SELECT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              LEFT JOIN pracownik_grupa pg ON u1.id = pg.id_pracownika
              LEFT JOIN grupy g ON pg.id_grupy = g.id
              WHERE z.status = 'zatwierdzone'";
    $result = mysqli_query($conn, $query);
    $substitutions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $substitutions[] = $row;
        }
    }
    mysqli_close($conn);
    return $substitutions;
}

function displaySchedule($startDate = null) {
    $schedule = getSchedule($startDate);
    $substitutions = getSubstitutions();
    $daysOfWeek = ['Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'];
    $groupedSchedule = [];
    $groupedSubstitutions = [];

    foreach ($schedule as $entry) {
        $groupedSchedule[$entry['grupa']][$entry['dzien']][] = $entry;
    }

    foreach ($substitutions as $entry) {
        $groupedSubstitutions[$entry['grupa']][$entry['data']][] = $entry;
    }

    echo '<table class="table schedule-table mt-4">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Grupa</th>';
    $currentDate = strtotime($startDate);
    for ($i = 0; $i < 7; $i++) {
        $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1];
        echo '<th>' . $dayOfWeek . '<br>' . date('Y-m-d', $currentDate) . '</th>';
        $currentDate = strtotime('+1 day', $currentDate);
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($groupedSchedule as $group => $days) {
        echo '<tr>';
        echo '<td>' . $group . '</td>';
        $currentDate = strtotime($startDate);
        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1];
            $currentDateStr = date('Y-m-d', $currentDate);
            echo '<td>';
            if (isset($days[$dayOfWeek])) {
                foreach ($days[$dayOfWeek] as $entry) {
                    echo '<div class="user-box">';
                    echo '<strong>' . $entry['imie'] . ' ' . $entry['nazwisko'] . '</strong><br />' . $entry['godzina_od'] . ' - ' . $entry['godzina_do'];
                    echo '</div>';
                }
            }
            if (isset($groupedSubstitutions[$group][$currentDateStr])) {
                foreach ($groupedSubstitutions[$group][$currentDateStr] as $entry) {
                    echo '<div class="user-box substitution" style="background-color: #ffffcc;">';
                    echo '<strong>' . $entry['imie_zastepujacego'] . ' ' . $entry['nazwisko_zastepujacego'] . '</strong><br />' . $entry['godzina_od'] . ' - ' . $entry['godzina_do'];
                    echo '<br /><em>Zastępstwo za: ' . $entry['imie_potrzebujacego'] . ' ' . $entry['nazwisko_potrzebujacego'] . '</em>';
                    echo '</div>';
                }
            }
            echo '</td>';
            $currentDate = strtotime('+1 day', $currentDate);
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
?>