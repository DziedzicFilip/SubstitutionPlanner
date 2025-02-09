<?php
require('../PHP_Logic/sidebar_logic.php');
require('../PHP_Logic/nadgodziny_logic.php');
if (!isset($_SESSION['user_id'])) {
    echo "Proszę się <a href='login.php'>zalogować</a>, aby uzyskać dostęp do tej strony.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nadgodziny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style_index.css" />
</head>
<body>
<div class="sidebar d-flex flex-column">
    <h4 class="text-center">Menu</h4>
    <a href="index.php">Harmonogram Grup</a>
    <?php if(isAdmin()) 
            echo '<a href="AktualneZastepstwa.php">Aktualne zastępstwa</a>';
    
    ?>   
    <a href="Nadgodziny.php">Nadgodziny</a>
    <a href="dodaj_zastepstwo.php">Dodaj zastępstwo</a>
    <?php 
    if(isAdmin())
    {
        echo "<a href='dodaj.php'>Dodaj</a>";
    }
    ?> 
    <div class="accordion mt-3" id="notificationAccordion">
        <?php 
        if(!isAdmin()) 
         {
            displaySubstitutionsAccept();
         }

        ?>
        
        <?php
        if(isAdmin())
        {
            displaySubstitutionsPending();
        }
        ?>
    </div>
    <div class="user-info mt-auto">
        <p>Zalogowany jako: <?php WhoAmI()?></p>
        <div class="links">
            <a href="ustawienia.php">Ustawienia</a>
            <form method="post" action="../PHP_Logic/logout.php" style="display:inline;">
                <button type="submit" class="btn btn-link">Wyloguj</button>
            </form>
        </div>
    </div>
</div>
<div class="content">
    <h2 class="text-center">Nadgodziny</h2>
    <div class="d-flex justify-content-center my-3 flex-wrap">
        <form method="get" action="Nadgodziny.php" class="d-flex">
            <?php if(isAdmin())
                echo '   <label for="searchUser" class="me-2">Wyszukaj użytkownika:</label>
            <input type="text" id="searchUser" name="searchUser" class="me-3" placeholder="Wpisz nazwę użytkownika"  />
           ';
            
            ?> 
          <label for="startDate" class="me-2">Od:</label>
            <input type="date" id="startDate" name="startDate" class="me-3"  />
            <label for="endDate" class="me-2">Do:</label>
            <input type="date" id="endDate" name="endDate" class="me-3"  />
            <button type="submit" class="btn btn-primary">Szukaj</button>
        </form>
    </div>
    <div id="overtimeContainer" class="row">
        <?php displayOvertimeCards($searchTerm, $startDate, $endDate); // wyswietalnie kart  ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>