<?php
require_once('database_connection.php');
require('../PHP_Logic/Logi/logMessage.php');
function getGroups() { // pobieranie grup 
    $conn = db_connect();
    $query = "SELECT id, nazwa FROM grupy";
    $result = mysqli_query($conn, $query);
    $groups = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
    }
    mysqli_close($conn);
    return $groups;
}

function wrtieGroups($groups) { // wypisanie 
    foreach ($groups as $group) {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="groups[]" value="' . $group['id'] . '" id="group' . $group['id'] . '">';
        echo '<label class="form-check-label" for="group' . $group['id'] . '">' . $group['nazwa'] . '</label>';
        echo '</div>';
    }
}

//dodawanie pracownika 
if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['login']) && isset($_POST['password']) && isset($_POST['email']))  {  // sprawdzanie czy dane są wpisane
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $groups = isset($_POST['groups']) ? $_POST['groups'] : []; // sprawdza czy instije grupy 
    $email = $_POST['email'];
    $conn = db_connect();

    // Sprawdzenie, czy login już istnieje
    $query = "SELECT id FROM uzytkownicy WHERE login = '$login'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 0) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO uzytkownicy (imie, nazwisko, login, haslo, rola, adresEmail) VALUES ('$firstName', '$lastName', '$login', '$password', 'pracownik', '$email')";
        if (mysqli_query($conn, $query)) {
            $userId = mysqli_insert_id($conn);

            // Dodanie grup do pracownika
            foreach ($groups as $groupId) {
                $query = "INSERT INTO pracownik_grupa (id_pracownika, id_grupy) VALUES ('$userId', '$groupId')";
                mysqli_query($conn, $query);
            }
//java script 
            logMessage('INFO', "Dodano nowego pracownika: $firstName $lastName", $_SESSION['user_id']);
            echo "<script>alert('Pracownik został pomyślnie dodany.'); window.location.href='../sites/dodaj.php';</script>";
        } else {
            echo "<script>alert('Błąd podczas dodawania pracownika.'); window.location.href='../sites/dodaj.php';</script>";
            logMessage('Error', "Błąd podczas dodawania pracownika", $_SESSION['user_id']);
        }
    } else {
        echo "<script>alert('Login już istnieje.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('Error', "Błąd podczas dodawania pracownika-Login już istnieje", $_SESSION['user_id']);
    }

    mysqli_close($conn);
}

if (isset($_POST['groupName'])) { //doddanie grup 
    $groupName = $_POST['groupName'];

    $conn = db_connect();

    $query = "SELECT id FROM grupy WHERE nazwa = '$groupName'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO grupy (nazwa) VALUES ('$groupName')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Grupa została pomyślnie dodana.'); window.location.href='../sites/dodaj.php';</script>";
            logMessage('INFO', "Dodano nową grupę: $groupName", $_SESSION['user_id']);
        } else {
            echo "<script>alert('Błąd podczas dodawania grupy.'); window.location.href='../sites/dodaj.php';</script>";
            logMessage('Error', "Błąd podczas dodawania grupy", $_SESSION['user_id']);
        }
    } else {
        echo "<script>alert('Nazwa grupy już istnieje.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('Error', "Błąd podczas dodawania grupy-Nazwa grupy już istnieje", $_SESSION['user_id']);
    }

    mysqli_close($conn);
}

function getUsers() { // pobranie uztykownikow 
    $conn = db_connect();
    $query = "SELECT id, CONCAT(imie, ' ', nazwisko) AS full_name FROM uzytkownicy where rola = 'pracownik'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['id'] . '">' . $row['full_name'] . '</option>';
        }
    }
    mysqli_close($conn);
}

//dodanie godzin pracy 
if (isset($_POST['employeeSelect']) && isset($_POST['dayOfWeek']) && isset($_POST['startTime']) && isset($_POST['endTime'])) {
    $employeeId = $_POST['employeeSelect'];
    $dayOfWeek = $_POST['dayOfWeek'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $groups = isset($_POST['groups']) ? $_POST['groups'] : [];

    $conn = db_connect();

    foreach ($groups as $groupId) {
        $query = "INSERT INTO harmonogram (id_pracownika, id_grupy, dzien, godzina_od, godzina_do) VALUES ('$employeeId', '$groupId', '$dayOfWeek', '$startTime', '$endTime')";
        mysqli_query($conn, $query);
    }

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>alert('Godziny pracy zostały pomyślnie dodane.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('INFO', "Dodano godziny pracy dla pracownika o id: $employeeId", $_SESSION['user_id']);
    } else {
        echo "<script>alert('Błąd podczas dodawania godzin pracy.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('Error', "Błąd podczas dodawania godzin pracy", $_SESSION['user_id']);
    }

    mysqli_close($conn);
}

if (isset($_POST['deleteEmployee'])) {     // Usunięcie pracownika 
    $employeeId = $_POST['manageEmployeeSelect'];

    $conn = db_connect();

    $query = "DELETE FROM uzytkownicy WHERE id = '$employeeId'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pracownik został pomyślnie usunięty.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('INFO', "Usunięto pracownika o id: $employeeId", $_SESSION['user_id']);
    } else {
        echo "<script>alert('Błąd podczas usuwania pracownika.'); window.location.href='../sites/dodaj.php';</script>";
        logMessage('Error', "Błąd podczas usuwania pracownika", $_SESSION['user_id']);
    }

    mysqli_close($conn);
}
?>