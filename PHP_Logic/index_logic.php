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

function getSubstitutions() { // funkja pobiera zastpestwa 
    $conn = db_connect();
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
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
    $schedule = getSchedule($startDate); // pobiera harmonogram od konkretnerj daty 
    $substitutions = getSubstitutions(); // pobiera zastepstwa
    $daysOfWeek = ['Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela'];
    $groupedSchedule = []; // tablica grupujaca harmonogram
    $groupedSubstitutions = []; // tablica grupujaca zastepstwa

    foreach ($schedule as $entry) { // grupuje harmonogram po grupie i dniu  zapisuje entry do tablicy tak aby byly grupowane po grupie i dniu
        $groupedSchedule[$entry['grupa']][$entry['dzien']][] = $entry;
    }

    foreach ($substitutions as $entry) {
        $groupedSubstitutions[$entry['grupa']][$entry['data']][] = $entry;
    }

    
   

    echo '<table class="table table-bordered">
    <thead>
   <tr>
    <th>Grupa</th>';
    $currentDate = strtotime($startDate); // zamiienia na unix dla latwijeszej operacji 
    for ($i = 0; $i < 7; $i++) {
        $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1]; // pobieranie dnia tygodnia 
        echo '<th>' . $dayOfWeek . '<br>' . date('Y-m-d', $currentDate) . '</th>'; // zmienia unixy na date 
        $currentDate = strtotime('+1 day', $currentDate); // opcja do iteracji po kolejnych dniach 
    }
    echo '</tr> </thead>
    <tbody>';// geracja wierszy tabeli dla harmongarmu i zastepstw
    foreach ($groupedSchedule as $group => $days) {  
        echo '<tr>';
        echo '<td>' . $group . '</td>';
        $currentDate = strtotime($startDate);
        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = $daysOfWeek[date('N', $currentDate) - 1];
            $currentDateStr = date('Y-m-d', $currentDate);
            echo '<td>';
            if (isset($days[$dayOfWeek])) { //sprawdzenie czy dane sa dla tego dnia 
                $uniqueEntries = []; // tablica unikalnych wartosci
                foreach ($days[$dayOfWeek] as $entry) { // interacja dla po wszytkich wydarzeniach dla danego dnia tygodnia 
                    $entryKey = $entry['imie'] . $entry['nazwisko'] . $entry['godzina_od'] . $entry['godzina_do']; // unikalny zapis 
                    if (!in_array($entryKey, $uniqueEntries)) { // sprawdznie czy wartosc juz jest w uniqueEntries
                        $uniqueEntries[] = $entryKey; // zapisanie zeby zapmietac ze juz ten wpis byl 
                        echo '<div class="user-box">';
                        echo '<strong>' . $entry['imie'] . ' ' . $entry['nazwisko'] . '</strong><br />' . $entry['godzina_od'] . ' - ' . $entry['godzina_do'];
                        echo '</div>';
                    }
                }
            }
            if (isset($groupedSubstitutions[$group][$currentDateStr])) { // sprawdza czy w tablic sa opowiednie  czyli  dla danej grupy i dnia sa zastepstwa
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
    echo '</tbody> </table>';
}
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d'); // obusluga daty poczatkowej
?>