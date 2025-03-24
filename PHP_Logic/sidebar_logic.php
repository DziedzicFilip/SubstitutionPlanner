<?php
session_start();
require_once('database_connection.php');
require_once('../PHP_Logic/Logi/logMessage.php');
// Obsługa zatwierdzania i odrzucania zastępstwa przez użytkownika
if (isset($_POST['substitution_id']) && isset($_POST['action'])) { // obsługa potwierdzania lub odrzucania zastępstwa z poziomu użytkownika
    $conn = db_connect(); // połączenie
    $substitution_id = $_POST['substitution_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    if ($action === 'accept') { 
        $status = 'zatwierdzone';
        $status2 = 'zaakceptowane'; // ustawienie zmiennej na zaakceptowane
        // zapytanie zmieniające status i dodające pracownika do tego zastępstwa
        $query = "UPDATE zastepstwa SET status = '$status', id_pracownika_zastepujacego = '$user_id' WHERE id = $substitution_id"; 
        if (mysqli_query($conn, $query)) {
            $substitutionQuery = "SELECT data_zastepstwa, godzina_od, godzina_do, nazwa_grupy FROM zastepstwa WHERE id = $substitution_id"; // data i godzina zastępstwa
            $substitutionResult = mysqli_query($conn, $substitutionQuery);
            if ($substitutionResult && mysqli_num_rows($substitutionResult) > 0) {
                $substitution = mysqli_fetch_assoc($substitutionResult);
                $date = $substitution['data_zastepstwa'];
                $startTime = $substitution['godzina_od'];
                $endTime = $substitution['godzina_do'];
                $groupName = $substitution['nazwa_grupy'];
                $hours = (strtotime($endTime) - strtotime($startTime)) / 3600;  // liczba godzin

                $overtimeQuery = "INSERT INTO nadgodziny (id_pracownika, data, liczba_godzin, nazwa_grupy) VALUES ('$user_id', '$date', '$hours', '$groupName')"; // dodanie do nadgodzin dla pracownika
                mysqli_query($conn, $overtimeQuery);

                // Zaktualizowanie statusu w tabeli zastepstwa_uzytkownicy
                $updateUserSubstitutionQuery = "UPDATE zastepstwa_uzytkownicy SET status = '$status2' WHERE id_zastepstwa = $substitution_id AND id_uzytkownika = '$user_id'";
                mysqli_query($conn, $updateUserSubstitutionQuery);

                // Odrzucenie innych użytkowników
                $rejectOthersQuery = "UPDATE zastepstwa_uzytkownicy SET status = 'odrzucone' WHERE id_zastepstwa = $substitution_id AND id_uzytkownika != '$user_id'";
                mysqli_query($conn, $rejectOthersQuery);
                logMessage("INFO", "Zastępstwo zostało zaakceptowane." .$substitution_id,$_SESSION['user_id']);
            }
        }
    } elseif ($action === 'reject') {
        $status = 'oczekujące';
     

        // Zaktualizowanie statusu w tabeli zastepstwa_uzytkownicy
        $updateUserSubstitutionQuery = "UPDATE zastepstwa_uzytkownicy SET status = 'odrzucone' WHERE id_zastepstwa = $substitution_id AND id_uzytkownika = '$user_id'";
        mysqli_query($conn, $updateUserSubstitutionQuery);
        logMessage("INFO", "Zastępstwo zostało odrzucone." .$substitution_id,$_SESSION['user_id']);
    } else { // obsługa dodatkowych opcji
        $status = 'oczekujące';
        $query = "UPDATE zastepstwa SET status = '$status' WHERE id = $substitution_id";
        mysqli_query($conn, $query);
    }

    mysqli_close($conn);

    header('Location: ../sites/index.php'); // przekierowanie na główną
    exit();
}

function isAdmin() { // sprawdzenie typu sesji
    checkRole();
    return isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin';
}

function checkRole() { // sprawdza rolę
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT rola FROM uzytkownicy WHERE id = '$user_id'"; // sprawdzenie roli użytkownika
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['rola'] = $row['rola']; 
        mysqli_close($conn);
        return $row['rola'] === 'admin';
    }
    
    mysqli_close($conn);
}

function WhoAmI() { // sprawdzenie, na kim jesteśmy zalogowani
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT imie, nazwisko FROM uzytkownicy WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_name = $row['imie'] . ' ' . $row['nazwisko'];
        echo $user_name;
    } else {
        echo "Nie znaleziono użytkownika.";
    }
    
    mysqli_close($conn);
}

function getSubstitutionsAccept() {
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT DISTINCT zu.id_zastepstwa AS id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, z.nazwa_grupy AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa_uzytkownicy zu
              JOIN zastepstwa z ON zu.id_zastepstwa = z.id
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              WHERE zu.status = 'oczekujące' AND zu.id_uzytkownika = '$user_id'";
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

function getSubstitutionsPending() {
    $conn = db_connect();
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, z.nazwa_grupy AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              WHERE z.status = 'oczekujące'";
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

function countSubstitutionsByAccept() { // liczenie zastępstw dla konkretnych pracowników
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT COUNT(*) AS count FROM zastepstwa_uzytkownicy WHERE status = 'oczekujące' AND id_uzytkownika = '$user_id'";
    $result = mysqli_query($conn, $query);
    $count = 0;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
    }
    mysqli_close($conn);
    return $count;
}

function countSubstitutionsByPending() { // liznie zastpst w dla admina 
    $conn = db_connect();
    $query = "SELECT COUNT(*) AS count FROM zastepstwa WHERE status = 'oczekujące'";
    $result = mysqli_query($conn, $query);
    $count = 0;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
    }
    mysqli_close($conn);
    return $count;
}

function displaySubstitutionsAccept() {
    $substitutions = getSubstitutionsAccept();
    echo ' <div class="accordion-item">
            <h2 class="accordion-header" id="headingUnassignedSubstitution">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnassignedSubstitution" aria-expanded="false" aria-controls="collapseUnassignedSubstitution">
                    Nowe zastępstwo
                    <span class="badge bg-primary rounded-circle ms-2">'. countSubstitutionsByAccept(); 
            echo '</span>  
                </button>
            </h2>
            <div id="collapseUnassignedSubstitution" class="accordion-collapse collapse" aria-labelledby="headingUnassignedSubstitution" data-bs-parent="#notificationAccordion">
               ';    echo '
            <div class="accordion-body">';
    foreach ($substitutions as $entry) {
        echo "Zastępstwo";
        echo '<p>' . $entry['data'] . ' od ' . $entry['godzina_od'] . ' do ' . $entry['godzina_do'] . '</p>';
        echo '<p>Grupa: ' . $entry['grupa'] . '</p>';
        echo '<hr />';
            echo '<form method="POST">';
            echo '<input type="hidden" name="substitution_id" value="' . $entry['id'] . '">';
            echo '<button type="submit" name="action" value="accept" class="btn btn-sm btn-warning">Akceptuj</button>';
            echo '<button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Odrzuć</button>';
            echo '</form>';
        
    }
    echo '  </div>
            </div>
        </div>';
}

function displaySubstitutionsPending() {
    $substitutions = getSubstitutionsPending();
    echo ' <div class="accordion-item">
            <h2 class="accordion-header" id="headingUnassignedSubstitution">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnassignedSubstitution" aria-expanded="false" aria-controls="collapseUnassignedSubstitution">
                    Nieprzypisane zastępstwo
                    <span class="badge bg-primary rounded-circle ms-2">'. countSubstitutionsByPending(); 
            echo '</span>       
                </button>
            </h2>                          
            <div id="collapseUnassignedSubstitution" class="accordion-collapse collapse" aria-labelledby="headingUnassignedSubstitution" data-bs-parent="#notificationAccordion">
               ';            
                 echo "  <a href='ZarzadzajZastepstwami.php' class='btn btn-primary mb-2'>Przypisz</a>" ; 
               echo '
            <div class="accordion-body">';
    foreach ($substitutions as $entry) {
        echo "Zastępstwo";
        echo '<p>' . $entry['data'] . ' od ' . $entry['godzina_od'] . ' do ' . $entry['godzina_do'] . '</p>';
        echo '<p>Grupa: ' . $entry['grupa'] . '</p>';
        echo '<hr />';      
    }
    echo '  </div>
            </div>
        </div>';
}
?>