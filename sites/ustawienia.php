<?php
require('../PHP_Logic/sidebar_logic.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ustawienia</title>
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
        <h2 class="text-center">Ustawienia</h2>
        <form method="post" action="../PHP_Logic/ustawienia_logic.php" >
            <div class="mb-3">
                <label for="currentLogin" class="form-label">Obecny Login</label>
                <input type="text" class="form-control" id="currentLogin" name="currentLogin" value="<?php echo $_SESSION['login']; ?>" readonly />
            </div>
            <div class="mb-3">
                <label for="newLogin" class="form-label">Nowy Login</label>
                <input type="text" class="form-control" id="newLogin" name="newLogin" required />
            </div>
            <div class="mb-3">
                <label for="currentPassword" class="form-label">Obecne Hasło</label>
                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required />
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label">Nowe Hasło</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required />
            </div>
            <button type="submit" class="btn btn-primary">Zmień Login i Hasło</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>