<?php
function db_connect() {
    $serwer = mysqli_connect("localhost", "root", "")
        or exit("Nie mozna połączyć się z serwerem bazy danych");

     mysqli_select_db($serwer, "zarzadzanie_harmonogramem3") 
        or exit("Nie mozna połączyć się z bazą zarzadzanie_harmonogramem");
    return $serwer;
}
?>
