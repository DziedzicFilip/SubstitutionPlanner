<?php 
require('../PHP_Logic/sidebar_logic.php');
require('../PHP_Logic/dodaj_zastepstwo_logic.php');
if (!isset($_SESSION['user_id'])) {
    echo "Proszę się <a href='login.php'>zalogować</a>, aby uzyskać dostęp do tej strony.";
    exit();
} 

$userGroups = getUserGroups($_SESSION['user_id']);
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
    <div class="form-container">
        <h2 class="text-center">Dodaj zastępstwo</h2>
        <form method="post" action="../PHP_Logic/dodaj_zastepstwo_logic.php">
    <div class="mb-3">
        <label for="date" class="form-label">Data</label>
        <input type="date" class="form-control" id="date" name="date" required>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Godzina rozpoczęcia</label>
        <input type="time" class="form-control" id="start_time" name="start_time" required>
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">Godzina zakończenia</label>
        <input type="time" class="form-control" id="end_time" name="end_time" required>
    </div>
    <?php if (isAdmin()) { ?>
    <div class="mb-3">
        <label for="employee_id" class="form-label">Pracownik</label>
        <select class="form-control" id="employee_id" name="employee_id" required onchange="loadGroups(this.value)">
            <option value="" disabled selected>Wybierz pracownika</option>
            <!-- Dodaj opcje pracowników -->
            <?php
            $conn = db_connect();
            $query = "SELECT id, CONCAT(imie, ' ', nazwisko) AS full_name FROM uzytkownicy WHERE rola = 'pracownik'";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['id'] . '">' . $row['full_name'] . '</option>';
            }
            mysqli_close($conn);
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="group" class="form-label">Grupa</label>
        <select class="form-control" id="group" name="group" required>
            <!-- Opcje grup będą ładowane dynamicznie -->
        </select>
    </div>
    <?php } else { ?>
    <div class="mb-3">
        <label for="group" class="form-label">Grupa</label>
        <select class="form-control" id="group" name="group" required>
            <!-- Dodaj opcje grup -->
            <?php
            $groups = getUserGroups($_SESSION['user_id']);
            foreach ($groups as $group) {
                echo '<option value="' . $group['nazwa'] . '">' . $group['nazwa'] . '</option>';
            }
            ?>
        </select>
    </div>
    <?php } ?>
  
    <button type="submit" class="btn btn-primary">Dodaj zastępstwo</button>
</form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadGroups(employeeId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../PHP_Logic/get_groups.php?employee_id=' + employeeId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var groups = JSON.parse(xhr.responseText);
            var groupSelect = document.getElementById('group');
            groupSelect.innerHTML = '';
            groups.forEach(function(group) {
                var option = document.createElement('option');
                option.value = group.nazwa;
                option.textContent = group.nazwa;
                groupSelect.appendChild(option);
            });
        }
    };
    xhr.send();
}
</script>
</body>
</html>