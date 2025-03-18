<?php
require_once('../PHP_Logic/database_connection.php');

function getSchedule($startDate = null) {
    $conn = db_connect();
    if ($startDate === null) {
        $startDate = date('Y-m-d');
    }
    $query = "SELECT DISTINCT g.nazwa AS grupa, u.imie, u.nazwisko, h.dzien, h.godzina_od, h.godzina_do
              FROM harmonogram h
              JOIN uzytkownicy u ON h.id_pracownika = u.id
              JOIN grupy g ON h.id_grupy = g.id
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
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, z.nazwa_grupy AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego, z.status
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              WHERE z.status IN ('zatwierdzone', 'DoAkceptacji')";
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
    $daysOfWeek = ['Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek'];
    $groupedSchedule = [];
    $groupedSubstitutions = [];

    foreach ($schedule as $entry) {
        $groupedSchedule[$entry['grupa']][$entry['dzien']][] = $entry;
    }

    foreach ($substitutions as $entry) {
        $groupedSubstitutions[$entry['grupa']][$entry['data']][] = $entry;
    }

    echo '<table class="table table-bordered">
    <thead>
    <tr>
    <th>Grupa</th>';

    $currentDate = strtotime('last Monday', strtotime($startDate));
    if (date('N', strtotime($startDate)) == 1) {
        $currentDate = strtotime($startDate);
    }

    for ($i = 0; $i < 5; $i++) {
        $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1];
        echo '<th>' . $dayOfWeek . '<br>' . date('Y-m-d', $currentDate) . '</th>';
        $currentDate = strtotime('+1 day', $currentDate);
    }
    echo '</tr> </thead>
    <tbody>';

    foreach ($groupedSchedule as $group => $days) {
        echo '<tr>';
        echo '<td>' . $group . '</td>';

        $currentDate = strtotime('last Monday', strtotime($startDate));
        if (date('N', strtotime($startDate)) == 1) {
            $currentDate = strtotime($startDate);
        }

        for ($i = 0; $i < 5; $i++) {
            $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1];
            $currentDateStr = date('Y-m-d', $currentDate);
            echo '<td>';
            if (isset($days[$dayOfWeek])) {
                $uniqueEntries = [];
                foreach ($days[$dayOfWeek] as $entry) {
                    $entryKey = $entry['imie'] . $entry['nazwisko'] . $entry['godzina_od'] . $entry['godzina_do'];
                    if (!in_array($entryKey, $uniqueEntries)) {
                        $uniqueEntries[] = $entryKey;
                        echo '<div class="user-box">';
                        echo '<strong>' . $entry['imie'] . ' ' . $entry['nazwisko'] . '</strong><br />' . $entry['godzina_od'] . ' - ' . $entry['godzina_do'];
                        echo '</div>';
                    }
                }
            }
            if (isset($groupedSubstitutions[$group][$currentDateStr])) {
                foreach ($groupedSubstitutions[$group][$currentDateStr] as $entry) {
                    $style = $entry['status'] == 'DoAkceptacji' ? 'background-color: #ffcccc;' : 'background-color: #ffffcc;';
                    echo '<div class="user-box substitution" style="' . $style . '">';
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
    echo '</tbody> </table>';
}

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');

?>