<?php 
require_once('../PHP_Logic/database_connection.php');
require_once('../PHP_Logic/Logi/logMessage.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revert'])) {
    $id = intval($_POST['revert']);
    updateSubstitutionStatus($id, 'oczekujace');
}

function getSubstitutions() { 
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

function getUsersActions()
{
    $conn = db_connect();
    $query = "
        SELECT 
            z.id AS zastepstwo_id, 
            z.data_zastepstwa AS data, 
            z.godzina_od, 
            z.godzina_do, 
            z.nazwa_grupy AS grupa,
            GROUP_CONCAT(CONCAT(u.imie, ' ', u.nazwisko, ' (', zu.status, ')') SEPARATOR ', ') AS osoby_status
        FROM 
            zastepstwa z
        JOIN 
            zastepstwa_uzytkownicy zu ON z.id = zu.id_zastepstwa
        JOIN 
            uzytkownicy u ON zu.id_uzytkownika = u.id
        WHERE 
            z.status = 'DoAkceptacji'
        GROUP BY 
            z.id
    ";
    $result = mysqli_query($conn, $query);
    $Actions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $Actions[] = $row;
        }
    }
    mysqli_close($conn);
    return $Actions;
}

function displayUsersActions()
{
    $Actions = getUsersActions();
    foreach ($Actions as $Action) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($Action['data']) . '</td>';
        echo '<td>' . htmlspecialchars($Action['grupa']) . '</td>';
        echo '<td>' . htmlspecialchars($Action['godzina_od']) . ' - ' . htmlspecialchars($Action['godzina_do']) . '</td>';
        echo '<td>' . htmlspecialchars($Action['osoby_status']) . '</td>';
        echo '<td>' . displayStatus($Action['zastepstwo_id']) . '</td>';
        echo '</tr>';
    }
}
function CountUserInSubstutions()
{
    $conn = db_connect();
    $query = "
        SELECT 
            z.id AS zastepstwo_id, 
            COUNT(zu.id_uzytkownika) AS count 
        FROM 
            zastepstwa z
        JOIN 
            zastepstwa_uzytkownicy zu ON z.id = zu.id_zastepstwa
        GROUP BY 
            z.id
    ";
    $result = mysqli_query($conn, $query);
    $counts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $counts[$row['zastepstwo_id']] = $row['count'];
        }
    }
  
    $result = mysqli_query($conn, $query);
    $counts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $counts[$row['zastepstwo_id']] = $row['count'];
        }
    }
    mysqli_close($conn);
    return $counts;
}
function coutUsersActions()
{
    $conn = db_connect();
    $query2 = "
    SELECT 
        z.id AS zastepstwo_id, 
        COUNT(zu.id_uzytkownika) AS count 
    FROM 
        zastepstwa z
    JOIN 
        zastepstwa_uzytkownicy zu ON z.id = zu.id_zastepstwa
       WHERE zu.status = 'odrzucone'
    GROUP BY 
        z.id 
 
";
$result2 = mysqli_query($conn, $query2);
$counts2 = [];
if ($result2 && mysqli_num_rows($result2) > 0) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $counts2[$row['zastepstwo_id']] = $row['count'];
    }
}
return $counts2;
}

function displayStatus($zastepstwo_id)
{
    $counts = CountUserInSubstutions();
    $counts2 = coutUsersActions();
    $output = '';

    if (isset($counts[$zastepstwo_id]) && isset($counts2[$zastepstwo_id]) && $counts[$zastepstwo_id] == $counts2[$zastepstwo_id]) {
        $output .= 'Wszystkie odrzucone <form method="post" action=""><button type="submit" name="revert" value="' . $zastepstwo_id . '">Cofnij</button></form>';
    }

    return $output;
}
function displaySubstitutions() { 
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
        echo '</div> </td>';
        echo '<td><form method="post" action=""><button type="submit" name="revert" value="' . $substitution['id'] . '">Cofnij</button></form></td>';
        echo '</tr>';
    }
} 
function updateSubstitutionStatus($id, $status) {
    $conn = db_connect();
    
    // Pobierz datę, nazwę grupy i id_pracownika_zastepujacego dla danego zastępstwa
    $query = "SELECT data_zastepstwa, nazwa_grupy, id_pracownika_zastepujacego FROM zastepstwa WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $data_zastepstwa, $nazwa_grupy, $id_pracownika_zastepujacego);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Zaktualizuj status w tabeli zastepstwa
    $query = "UPDATE zastepstwa SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Zaktualizuj status w tabeli nadgodziny
    $query = "UPDATE nadgodziny SET status = 'cofniete' WHERE data = ? AND nazwa_grupy = ? AND id_pracownika = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $data_zastepstwa, $nazwa_grupy, $id_pracownika_zastepujacego);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Debugowanie: Sprawdź, czy zapytanie SQL jest wykonywane bez błędów
    if (mysqli_errno($conn)) {
        echo "Błąd SQL: " . mysqli_error($conn);
        error_log("Błąd SQL: " . mysqli_error($conn));
    }

    mysqli_close($conn);

    logMessage('INFO-AktualneZastepstwa', 'Cofnięto zastępstwo i zaktualizowano nadgodziny', $_SESSION['user_id']);
}
?>