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
        <form method="get" action="Nadgodziny.php" class="d-flex flex-wrap">
            <?php if(isAdmin()) echo '<input type="text" name="searchUser" placeholder="Search User" value="' . htmlspecialchars($searchTerm) . '" class="form-control mb-2 me-2">'; ?>
            <input type="date" name="startDate" value="<?php echo htmlspecialchars($startDate); ?>" class="form-control mb-2 me-2">
            <input type="date" name="endDate" value="<?php echo htmlspecialchars($endDate); ?>" class="form-control mb-2 me-2">
            <button type="submit" class="btn btn-primary mb-2 me-2">Search</button>
            <button type="submit" name="generatePDF" value="1" class="btn btn-secondary mb-2">Generate PDF</button>
        </form>
    </div>
    <div id="overtimeContainer" class="row">
        <?php displayOvertimeCards($searchTerm, $startDate, $endDate); // wyswietalnie kart ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>