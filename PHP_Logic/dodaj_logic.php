<?php
require_once('database_connection.php');

function getGroups() {
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
function wrtieGroups($groups) {
    foreach ($groups as $group) {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="groups[]" value="' . $group['id'] . '" id="group' . $group['id'] . '">';
        echo '<label class="form-check-label" for="group' . $group['id'] . '">' . $group['nazwa'] . '</label>';
        echo '</div>';
    }
}

if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['login']) && isset($_POST['password']) ) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $groups = isset($_POST['groups']) ? $_POST['groups'] : [];

    $conn = db_connect();

    // Sprawdzenie, czy login już istnieje
    $query = "SELECT id FROM uzytkownicy WHERE login = '$login'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 0) {
        // Dodanie pracownika
        $query = "INSERT INTO uzytkownicy (imie, nazwisko, login, haslo, rola) VALUES ('$firstName', '$lastName', '$login', '$password', 'pracownik')";
        if (mysqli_query($conn, $query)) {
            $userId = mysqli_insert_id($conn);

            // Dodanie grup do pracownika
            foreach ($groups as $groupId) {
                $query = "INSERT INTO pracownik_grupa (id_pracownika, id_grupy) VALUES ('$userId', '$groupId')";
                mysqli_query($conn, $query);
            }

            echo "<script>alert('Pracownik został pomyślnie dodany.'); window.location.href='../sites/dodaj.php';</script>";
        } else {
            echo "<script>alert('Błąd podczas dodawania pracownika.'); window.location.href='../sites/dodaj.php';</script>";
        }
    } else {
        echo "<script>alert('Login już istnieje.'); window.location.href='../sites/dodaj.php';</script>";
    }

    mysqli_close($conn);
}


if (isset($_POST['groupName'])) {
    $groupName = $_POST['groupName'];

    $conn = db_connect();

    
    $query = "SELECT id FROM grupy WHERE nazwa = '$groupName'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 0) {
        
        $query = "INSERT INTO grupy (nazwa) VALUES ('$groupName')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Grupa została pomyślnie dodana.'); window.location.href='../sites/dodaj.php';</script>";
        } else {
            echo "<script>alert('Błąd podczas dodawania grupy.'); window.location.href='../sites/dodaj.php';</script>";
        }
    } else {
        echo "<script>alert('Nazwa grupy już istnieje.'); window.location.href='../sites/dodaj.php';</script>";
    }

    mysqli_close($conn);
}


 
function getUsers() {
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



if (isset($_POST['employeeSelect']) && isset($_POST['dayOfWeek']) && isset($_POST['startTime']) && isset($_POST['endTime'])) {
    $employeeId = $_POST['employeeSelect'];
    $dayOfWeek = $_POST['dayOfWeek'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $groups = isset($_POST['groups']) ? $_POST['groups'] : [];

    $conn = db_connect();

    // Dodanie godzin pracy
    $query = "INSERT INTO harmonogram (id_pracownika, dzien, godzina_od, godzina_do) VALUES ('$employeeId', '$dayOfWeek', '$startTime', '$endTime')";
    if (mysqli_query($conn, $query)) {
        // Dodanie grup do harmonogramu
        foreach ($groups as $groupId) {
            $query = "INSERT INTO pracownik_grupa (id_pracownika, id_grupy) VALUES ('$employeeId', '$groupId')";
            mysqli_query($conn, $query);
        }
        echo "<script>alert('Godziny pracy zostały pomyślnie dodane.'); window.location.href='../sites/dodaj.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas dodawania godzin pracy.'); window.location.href='../sites/dodaj.php';</script>";
    }

    mysqli_close($conn);
}

if (isset($_POST['deleteEmployee'])) {
    $employeeId = $_POST['manageEmployeeSelect'];

    $conn = db_connect();

    // Usunięcie pracownika
    $query = "DELETE FROM uzytkownicy WHERE id = '$employeeId'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pracownik został pomyślnie usunięty.'); window.location.href='../sites/dodaj.php';</script>";
    } else {
        echo "<script>alert('Błąd podczas usuwania pracownika.'); window.location.href='../sites/dodaj.php';</script>";
    }

    mysqli_close($conn);
}


?>