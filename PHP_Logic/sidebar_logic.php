<?php
session_start();
require('database_connection.php');
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
        
        $queryName = "SELECT imie FROM uzytkownicy WHERE id = '$user_id'";
        $queryForName = "SELECT nazwisko FROM uzytkownicy WHERE id = '$user_id'";
        
        $resultName = mysqli_query($conn, $queryName);
        $resultForName = mysqli_query($conn, $queryForName);
        
        if ($resultName && mysqli_num_rows($resultName) > 0 && $resultForName && mysqli_num_rows($resultForName) > 0) {
            $rowName = mysqli_fetch_assoc($resultName);
            $rowForName = mysqli_fetch_assoc($resultForName);
            
            $user_name = $rowName['imie'] . ' ' . $rowForName['nazwisko'];
            echo $user_name;
        } else {
            echo "Nie znaleziono użytkownika.";
        }
        
        mysqli_close($conn);
    }
    

   

?>