<?php
session_start();
require_once('database_connection.php');

if (isset($_POST['substitution_id']) && isset($_POST['action'])) {
    $conn = db_connect();
    $substitution_id = $_POST['substitution_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    if ($action === 'accept') {
        $status = 'zatwierdzone';
        $query = "UPDATE zastepstwa SET status = '$status', id_pracownika_zastepujacego = '$user_id' WHERE id = $substitution_id";
        if (mysqli_query($conn, $query)) {
            // Fetch substitution details
            $substitutionQuery = "SELECT data_zastepstwa, godzina_od, godzina_do FROM zastepstwa WHERE id = $substitution_id";
            $substitutionResult = mysqli_query($conn, $substitutionQuery);
            if ($substitutionResult && mysqli_num_rows($substitutionResult) > 0) {
                $substitution = mysqli_fetch_assoc($substitutionResult);
                $date = $substitution['data_zastepstwa'];
                $startTime = $substitution['godzina_od'];
                $endTime = $substitution['godzina_do'];
                $hours = (strtotime($endTime) - strtotime($startTime)) / 3600; // Calculate hours

                // Insert into nadgodziny table
                $overtimeQuery = "INSERT INTO nadgodziny (id_pracownika, data, liczba_godzin) VALUES ('$user_id', '$date', '$hours')";
                mysqli_query($conn, $overtimeQuery);
            }
        }
    } elseif ($action === 'reject') {
        $status = 'oczekujące';
        $query = "UPDATE zastepstwa SET status = '$status', id_pracownika_zastepujacego = NULL WHERE id = $substitution_id";
        mysqli_query($conn, $query);
    } else {
        $status = 'oczekujące';
        $query = "UPDATE zastepstwa SET status = '$status' WHERE id = $substitution_id";
        mysqli_query($conn, $query);
    }

    mysqli_close($conn);

    header('Location: ../sites/index.php');
    exit();
}

function isAdmin() {
    checkRole();
    return isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin';
}

function checkRole() {
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT rola FROM uzytkownicy WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['rola'] = $row['rola']; 
        mysqli_close($conn);
        return $row['rola'] === 'admin';
    }
    
    mysqli_close($conn);
}

function WhoAmI() {
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

function getSubstitutionsByStatus($status) {
    $conn = db_connect();
  
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              LEFT JOIN pracownik_grupa pg ON u1.id = pg.id_pracownika
              LEFT JOIN grupy g ON pg.id_grupy = g.id
              WHERE z.status = '$status'";
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

function getSubstitutionsAccept() {
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              LEFT JOIN pracownik_grupa pg ON u1.id = pg.id_pracownika
              LEFT JOIN grupy g ON pg.id_grupy = g.id
              WHERE z.status = 'DoAkceptacji' AND z.id_pracownika_zastepujacego = '$user_id'";
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
    $query = "SELECT DISTINCT z.id, z.data_zastepstwa AS data, z.godzina_od, z.godzina_do, g.nazwa AS grupa, 
                     u1.imie AS imie_potrzebujacego, u1.nazwisko AS nazwisko_potrzebujacego, 
                     u2.imie AS imie_zastepujacego, u2.nazwisko AS nazwisko_zastepujacego
              FROM zastepstwa z
              JOIN uzytkownicy u1 ON z.id_pracownika_proszacego = u1.id
              LEFT JOIN uzytkownicy u2 ON z.id_pracownika_zastepujacego = u2.id
              LEFT JOIN pracownik_grupa pg ON u1.id = pg.id_pracownika
              LEFT JOIN grupy g ON pg.id_grupy = g.id
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

function countSubstitutionsByAccept() {
    $conn = db_connect();
    $user_id = $_SESSION['user_id'];
    $query = "SELECT COUNT(*) AS count FROM zastepstwa WHERE status = 'DoAkceptacji' AND id_pracownika_zastepujacego = '$user_id'";
    $result = mysqli_query($conn, $query);
    $count = 0;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
    }
    mysqli_close($conn);
    return $count;
}

function countSubstitutionsByPending() {
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
                    Nieprzypisane zastępstwo
                    <span class="badge bg-primary rounded-circle ms-2">'. countSubstitutionsByAccept(); 
            echo '</span>
                    
                </button>
                
           
            </h2>
                        
            
            <div id="collapseUnassignedSubstitution" class="accordion-collapse collapse" aria-labelledby="headingUnassignedSubstitution" data-bs-parent="#notificationAccordion">
               ';
               
                 
               echo '
            <div class="accordion-body">';
    foreach ($substitutions as $entry) {
        echo '<p>' . $entry['data'] . ' od ' . $entry['godzina_od'] . ' do ' . $entry['godzina_do'] . '</p>';
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
        echo '<p>' . $entry['data'] . ' od ' . $entry['godzina_od'] . ' do ' . $entry['godzina_do'] . '</p>';
        echo '<hr />';
        
    }
    echo '  </div>
            </div>
        </div>';
}
?>