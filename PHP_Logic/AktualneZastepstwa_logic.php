<?php 
require_once('../PHP_Logic/database_connection.php');

function getSubstitutions() { // pobieranie wartosci z bazy danych dotyczace zastepstw
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

function displaySubstitutions() { //wyswietlanie zastepstw
    $substitutions = getSubstitutions();
    $uniqueSubstitutions = [];
    foreach ($substitutions as $substitution) { //zapeniwie o niepowtarzaniu sie zastepstw
        $uniqueKey = $substitution['id'];
        if (!isset($uniqueSubstitutions[$uniqueKey])) {
            $uniqueSubstitutions[$uniqueKey] = $substitution;
        }
    }
    foreach ($uniqueSubstitutions as $substitution) {
        echo '<tr> <td>' . $substitution['data'] . '</td>';
        echo '<td>' . $substitution['grupa'] . '</td> <td>';
        echo '<div class="user-box">';
        echo '<strong>' . $substitution['imie_potrzebujacego'] . ' ' 
        . $substitution['nazwisko_potrzebujacego'] . '</strong><br />' . $substitution['godzina_od'] . ' - ' . $substitution['godzina_do'];
        echo '</div> </td><td>';
        echo '<div class="user-box">';
        echo '<strong>' . $substitution['imie_zastepujacego'] . ' ' 
        . $substitution['nazwisko_zastepujacego'] . '</strong><br />' . $substitution['godzina_od'] . ' - ' . $substitution['godzina_do'];
        echo '</div> </td> </tr>';
    }
}
?>