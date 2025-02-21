<?php
require('../PHP_Logic/sidebar_logic.php'); // logika dzialania sidebar 
require('../PHP_Logic/index_logic.php'); // logika dzialania  index 
if (!isset($_SESSION['user_id'])) {
    echo "Proszę się <a href='login.php'>zalogować</a>, aby uzyskać dostęp do tej strony.";
    exit();
} // warunek ktory sprawdza czy uzytkownik jest zalogowany

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
    <?php if(isAdmin())  // z sidebar sprawdza role 
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
            displaySubstitutionsAccept(); // wyswietla zastepstwa do akceptacji
         }

        ?>
        
        <?php
        if(isAdmin())
        {
            displaySubstitutionsPending(); // wyswietla zastepstwa oczekujace
        }
        ?>
    </div>
    <div class="user-info mt-auto">
        <p>Zalogowany jako: <?php WhoAmI()  // wyswietla jako kot jestesmy zalogowani?></p> 
        <div class="links">
            <a href="ustawienia.php">Ustawienia</a>
            <form method="post" action="../PHP_Logic/logout.php" style="display:inline;">
                <button type="submit" class="btn btn-link">Wyloguj</button>
            </form>
        </div>
    </div>
</div>
<div class="content">
    <h2 class="text-center">Harmonogram Grup</h2>
    <form method="get" action="index.php" class="mb-4">
        <label for="startDate">Wybierz datę początkową:</label>
        <input type="date" id="startDate" name="startDate" value="<?php echo $startDate; ?>" class="form-control mb-2" style='width:30%;' />
        <button type="submit" class="btn btn-primary">Pokaż</button>
    </form>
    <div class="table-responsive">
        <?php displaySchedule($startDate);   ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>