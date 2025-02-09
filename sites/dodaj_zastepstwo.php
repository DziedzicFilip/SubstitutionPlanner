<?php 
require('../PHP_Logic/sidebar_logic.php');
require('../PHP_Logic/dodaj_zastepstwo_logic.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Harmonogram Grup</title>
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
    <h2 class="text-center">Dodaj zastepstwo</h2>
    <form method="POST" action="../PHP_Logic/dodaj_zastepstwo_logic.php">
    <label for="date">Data:</label>
    <input type="date" id="date" name="date" required>
    
    <label for="start_time">Godzina Od:</label>
    <input type="time" id="start_time" name="start_time" required>
    
    <label for="end_time">Godzina Do:</label>
    <input type="time" id="end_time" name="end_time" required>
    
 
    
    <button type="submit">Dodaj Zastępstwo</button>
</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>