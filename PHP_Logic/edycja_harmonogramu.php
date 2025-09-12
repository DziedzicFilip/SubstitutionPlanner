<?php
require_once('database_connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rozbij osobę na imię i nazwisko
    list($old_imie, $old_nazwisko) = explode(' ', $_POST['old_osoba'], 2);
    $old_grupa = $_POST['old_grupa'];
    $old_dzien = $_POST['old_dzien'];

    list($imie, $nazwisko) = explode(' ', $_POST['osoba'], 2);
    $grupa = $_POST['grupa'];
    $dzien = $_POST['dzien'];
    $od = $_POST['od'];
    $do = $_POST['do'];

    $conn = db_connect();

    // Pobierz stare id_pracownika i id_grupy
    $stmt = $conn->prepare("SELECT u.id, g.id FROM uzytkownicy u JOIN grupy g ON g.nazwa=? WHERE u.imie=? AND u.nazwisko=?");
    $stmt->bind_param("sss", $old_grupa, $old_imie, $old_nazwisko);
    $stmt->execute();
    $stmt->bind_result($old_id_pracownika, $old_id_grupy);
    $stmt->fetch();
    $stmt->close();

    // Pobierz nowe id_pracownika i id_grupy
    $stmt = $conn->prepare("SELECT u.id, g.id FROM uzytkownicy u JOIN grupy g ON g.nazwa=? WHERE u.imie=? AND u.nazwisko=?");
    $stmt->bind_param("sss", $grupa, $imie, $nazwisko);
    $stmt->execute();
    $stmt->bind_result($new_id_pracownika, $new_id_grupy);
    $stmt->fetch();
    $stmt->close();

    // Aktualizuj harmonogram
    $stmt2 = $conn->prepare("UPDATE harmonogram SET id_pracownika=?, id_grupy=?, dzien=?, godzina_od=?, godzina_do=? WHERE id_pracownika=? AND id_grupy=? AND dzien=?");
    $stmt2->bind_param("iisssiis", $new_id_pracownika, $new_id_grupy, $dzien, $od, $do, $old_id_pracownika, $old_id_grupy, $old_dzien);
    $stmt2->execute();
    $stmt2->close();

    $conn->close();
    header("Location: ../sites/index.php");
    exit();
}
?>