<?php
function db_connect() {
    $serwer = mysqli_connect("localhost", "root", "")
        or exit("Nie mozna połączyć się z serwerem bazy danych");

    $baza = mysqli_select_db($serwer, "zarzadzanie_harmonogramem") 
        or exit("Nie mozna połączyć się z bazą zarzadzanie_harmonogramem");
    return $serwer;
}
?>
