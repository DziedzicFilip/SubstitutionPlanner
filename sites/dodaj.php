<?php
require('../PHP_Logic/sidebar_logic.php');
require('../PHP_Logic/dodaj_logic.php');

if (!isset($_SESSION['user_id'])) {
    echo "Proszę się <a href='login.php'>zalogować</a>, aby uzyskać dostęp do tej strony.";
    exit();
}



$groups = getGroups();

?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Dodaj</title>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
			rel="stylesheet"
		/>
		<link rel="stylesheet" href="style_index.css" />
		<style>
			.card-body {
				max-width: 400px;
				margin: 0 auto;
			}
		</style>
	</head>
	<body>
		<div class="sidebar d-flex flex-column">
			<h4 class="text-center">Menu</h4>
			<a href="index.php">Harmonogram Grup</a>
			<a href="AktualneZastepstwa.php">Aktualne zastępstwa</a>
			<a href="Nadgodziny.php">Nadgodziny</a>
			<?php 
			if(isAdmin())
			{
				echo "<a href='dodaj.php'>Dodaj</a>";
			}
			
			?> 
			
			<div class="accordion mt-3" id="notificationAccordion">
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingNewSubstitution">
						<button
							class="accordion-button"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#collapseNewSubstitution"
							aria-expanded="true"
							aria-controls="collapseNewSubstitution"
						>
							Nowe zastępstwo
							<span class="badge bg-primary rounded-circle ms-2">3</span>
						</button>
					</h2>
					<div
						id="collapseNewSubstitution"
						class="accordion-collapse collapse"
						aria-labelledby="headingNewSubstitution"
						data-bs-parent="#notificationAccordion"
					>
						<div class="accordion-body">
							<p>Nowe zastępstwo na 2023-10-06 od 10:00 do 14:00</p>
							<button class="btn btn-sm btn-warning">Akceptuj</button>
							<button class="btn btn-sm btn-danger">Usuń</button>
							<hr />
							<p>Nowe zastępstwo na 2023-10-07 od 09:00 do 13:00</p>
							<button class="btn btn-sm btn-warning">Akceptuj</button>
							<button class="btn btn-sm btn-danger">Usuń</button>
							<hr />
							<p>Nowe zastępstwo na 2023-10-08 od 12:00 do 16:00</p>
							<button class="btn btn-sm btn-warning">Akceptuj</button>
							<button class="btn btn-sm btn-danger">Usuń</button>
						</div>
					</div>
				</div>
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingAcceptedSubstitution">
						<button
							class="accordion-button collapsed"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#collapseAcceptedSubstitution"
							aria-expanded="false"
							aria-controls="collapseAcceptedSubstitution"
						>
							Zakceptowane zastępstwo
							<span class="badge bg-primary rounded-circle ms-2">2</span>
						</button>
					</h2>
					<div
						id="collapseAcceptedSubstitution"
						class="accordion-collapse collapse"
						aria-labelledby="headingAcceptedSubstitution"
						data-bs-parent="#notificationAccordion"
					>
						<div class="accordion-body">
							<p>Zakceptowane zastępstwo na 2023-10-05 od 08:00 do 12:00</p>
							<button class="btn btn-sm btn-danger">Usuń</button>
							<hr />
							<p>Zakceptowane zastępstwo na 2023-10-09 od 14:00 do 18:00</p>
							<button class="btn btn-sm btn-danger">Usuń</button>
						</div>
					</div>
				</div>
				<?php
				if(isAdmin())
				 echo '
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingUnassignedSubstitution">
						<button
							class="accordion-button collapsed"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#collapseUnassignedSubstitution"
							aria-expanded="false"
							aria-controls="collapseUnassignedSubstitution"
						>
							Nieprzypisane zastępstwo
							<span class="badge bg-primary rounded-circle ms-2">2</span>
						</button>
					</h2>
					<div
						id="collapseUnassignedSubstitution"
						class="accordion-collapse collapse"
						aria-labelledby="headingUnassignedSubstitution"
						data-bs-parent="#notificationAccordion"
					>
						<div class="accordion-body">
							<p>Brak osoby do zastępstwa na 2023-10-07 od 09:00 do 13:00</p>
							<button class="btn btn-sm btn-warning">Akceptuj</button>
							<button class="btn btn-sm btn-danger">Usuń</button>
							<hr />
							<p>Brak osoby do zastępstwa na 2023-10-10 od 11:00 do 15:00</p>
							<button class="btn btn-sm btn-warning">Akceptuj</button>
							<button class="btn btn-sm btn-danger">Usuń</button>
						</div>
					</div>
				</div> '
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
		<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="text-center">Dodaj Pracownika</h2>
                <form method="post" action="../PHP_Logic/dodaj_logic.php">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">Imię</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Nazwisko</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Hasło</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="groups" class="form-label">Grupy</label>
                        <div id="groups">
						<?php
								wrtieGroups($groups);
                    ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Dodaj Pracownika</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="text-center">Dodaj Grupę</h2>
                <form method="post" action="../PHP_Logic/dodaj_logic.php">
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Nazwa Grupy</label>
                        <input type="text" class="form-control" id="groupName" name="groupName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Dodaj Grupę</button>
                </form>
            </div>
        </div>
    </div>
	<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="text-center">Dodaj Godziny Pracy</h2>
            <form method="post" action="../PHP_Logic/dodaj_logic.php">
                <div class="mb-3">
                    <label for="employeeSelect" class="form-label">Pracownik</label>
                    <select class="form-control" id="employeeSelect" name="employeeSelect" required>
                        <?php getUsers(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="dayOfWeek" class="form-label">Dzień Tygodnia</label>
                    <select class="form-control" id="dayOfWeek" name="dayOfWeek" required>
                        <option value="Poniedziałek">Poniedziałek</option>
                        <option value="Wtorek">Wtorek</option>
                        <option value="Środa">Środa</option>
                        <option value="Czwartek">Czwartek</option>
                        <option value="Piątek">Piątek</option>
                        <option value="Sobota">Sobota</option>
                        <option value="Niedziela">Niedziela</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="groups" class="form-label">Grupa</label>
                    <?php wrtieGroups($groups); ?>
                </div>
                <div class="mb-3">
                    <label for="startTime" class="form-label">Godzina Rozpoczęcia</label>
                    <input type="time" class="form-control" id="startTime" name="startTime" required>
                </div>
                <div class="mb-3">
                    <label for="endTime" class="form-label">Godzina Zakończenia</label>
                    <input type="time" class="form-control" id="endTime" name="endTime" required>
                </div>
                <button type="submit" class="btn btn-primary">Dodaj Godziny Pracy</button>
            </form>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="text-center">Zarządzaj Pracownikiem</h2>
            <form method="post" action="../PHP_Logic/dodaj_logic.php">
                <div class="mb-3">
                    <label for="manageEmployeeSelect" class="form-label">Pracownik</label>
                    <select class="form-control" id="manageEmployeeSelect" name="manageEmployeeSelect" required>
                        <?php getUsers(); ?>
                    </select>
                </div>
                <div id="employeeDetails" style="display: none;">
                    <h5>Grupy:</h5>
                    <ul id="employeeGroups"></ul>
                    <h5>Godziny Pracy:</h5>
                    <ul id="employeeHours"></ul>
                </div>
                
                <button type="submit" name="deleteEmployee" class="btn btn-danger">Usuń Pracownika</button>
            </form>
        </div>
    </div>
</div>
</div>
            </div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		
	</body>
</html>
