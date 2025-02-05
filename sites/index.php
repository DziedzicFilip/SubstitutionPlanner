<?php
require('../PHP_Logic/sidebar_logic.php');
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
		<title>Harmonogram Grup</title>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
			rel="stylesheet"
		/>
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
		/>
		<link rel="stylesheet" href="style_index.css" />
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
			<div class="d-flex justify-content-between align-items-center">
				<h2 class="text-center flex-grow-1">Harmonogram Grup</h2>
			</div>
			<div class="d-flex justify-content-center my-3 flex-wrap">
				<label for="startDate" class="me-2">Wybierz datę od:</label>
				<input type="date" id="startDate" class="me-3" />
				<button class="btn btn-primary ms-3">Zastosuj</button>
			</div>
			<div class="table-responsive">
				<table class="table schedule-table mt-4">
					<thead>
						<tr>
							<th>Grupa</th>
							<th>Poniedziałek<br />27 sty</th>
							<th>Wtorek<br />28 sty</th>
							<th>Środa<br />29 sty</th>
							<th>Czwartek<br />30 sty</th>
							<th>Piątek<br />31 sty</th>
							<th>Sobota<br />1 lut</th>
							<th>Niedziela<br />2 lut</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Grupa 1</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Jan Kowalski: 08:00 - 12:00')"
								>
									<strong>Jan Kowalski</strong><br />08:00 - 12:00
								</div>
								<div
									class="user-box"
									onclick="alert('Anna Nowak: 12:00 - 16:00')"
								>
									<strong>Anna Nowak</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Anna Nowak: 10:00 - 14:00')"
								>
									<strong>Anna Nowak</strong><br />10:00 - 14:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Piotr Zieliński: 12:00 - 16:00')"
								>
									<strong>Piotr Zieliński</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Maria Lewandowska: 09:00 - 13:00')"
								>
									<strong>Maria Lewandowska</strong><br />09:00 - 13:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Adam Wiśniewski: 14:00 - 18:00')"
								>
									<strong>Adam Wiśniewski</strong><br />14:00 - 18:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Jan Kowalski: 08:00 - 12:00')"
								>
									<strong>Jan Kowalski</strong><br />08:00 - 12:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Anna Nowak: 10:00 - 14:00')"
								>
									<strong>Anna Nowak</strong><br />10:00 - 14:00
								</div>
							</td>
						</tr>
						<tr>
							<td>Grupa 2</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Katarzyna Dąbrowska: 08:00 - 12:00')"
								>
									<strong>Katarzyna Dąbrowska</strong><br />08:00 - 12:00
								</div>
								<div
									class="user-box"
									onclick="alert('Tomasz Wójcik: 12:00 - 16:00')"
								>
									<strong>Tomasz Wójcik</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Monika Pawlak: 10:00 - 14:00')"
								>
									<strong>Monika Pawlak</strong><br />10:00 - 14:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Robert Jankowski: 12:00 - 16:00')"
								>
									<strong>Robert Jankowski</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Karolina Woźniak: 09:00 - 13:00')"
								>
									<strong>Karolina Woźniak</strong><br />09:00 - 13:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Grzegorz Lis: 14:00 - 18:00')"
								>
									<strong>Grzegorz Lis</strong><br />14:00 - 18:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Magdalena Kowalczyk: 08:00 - 12:00')"
								>
									<strong>Magdalena Kowalczyk</strong><br />08:00 - 12:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Łukasz Nowicki: 10:00 - 14:00')"
								>
									<strong>Łukasz Nowicki</strong><br />10:00 - 14:00
								</div>
							</td>
						</tr>
						<tr>
							<td>Grupa 3</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Patryk Mazur: 08:00 - 12:00')"
								>
									<strong>Patryk Mazur</strong><br />08:00 - 12:00
								</div>
								<div
									class="user-box"
									onclick="alert('Justyna Górska: 12:00 - 16:00')"
								>
									<strong>Justyna Górska</strong><br />12:00 - 16:00
								</div>
								<div
									class="user-box"
									onclick="alert('Justyna Górska: 12:00 - 16:00')"
								>
									<strong>Justyna Górska</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Wojciech Malinowski: 10:00 - 14:00')"
								>
									<strong>Wojciech Malinowski</strong><br />10:00 - 14:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Elżbieta Czarnecka: 12:00 - 16:00')"
								>
									<strong>Elżbieta Czarnecka</strong><br />12:00 - 16:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Kamil Szymański: 09:00 - 13:00')"
								>
									<strong>Kamil Szymański</strong><br />09:00 - 13:00
								</div>
								<div
									class="user-box"
									onclick="alert('Wojciech Malinowski: 10:00 - 14:00')"
								>
									<strong>Wojciech Malinowski</strong><br />10:00 - 14:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Paweł Włodarczyk: 14:00 - 18:00')"
								>
									<strong>Paweł Włodarczyk</strong><br />14:00 - 18:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Marta Olejniczak: 08:00 - 12:00')"
								>
									<strong>Marta Olejniczak</strong><br />08:00 - 12:00
								</div>
							</td>
							<td>
								<div
									class="user-box"
									onclick="alert('Barbara Kaczmarek: 10:00 - 14:00')"
								>
									<strong>Barbara Kaczmarek</strong><br />10:00 - 14:00
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- Modal -->
		<div
			class="modal fade"
			id="substitutionModal"
			tabindex="-1"
			aria-labelledby="substitutionModalLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="substitutionModalLabel">
							Dostępne Zastępstwa
						</h5>
						<button
							type="button"
							class="btn-close"
							data-bs-dismiss="modal"
							aria-label="Close"
						></button>
					</div>
					<div class="modal-body">
						<!-- Content will be dynamically loaded here -->
						<div id="substitutionContent">
							<!-- Example content with checkboxes and available substitutes -->
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									value="Anna Nowak"
									id="substitution1"
								/>

								<p>Możliwe zastępstwa: Jan Kowalski</p>
							</div>
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									value="Jan Kowalski"
									id="substitution2"
								/>

								<p>Możliwe zastępstwa: Maria Lewandowska</p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary"
							data-bs-dismiss="modal"
						>
							Anuluj
						</button>
						<button type="button" class="btn btn-primary">Wyślij</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Add this script at the end of the <body> tag -->
		<script>
			document.querySelectorAll('.user-box').forEach((box) => {
				box.addEventListener('click', function () {
					// Load dynamic content here if needed
					// For example, you can use AJAX to fetch data based on the user

					// Show the modal
					const substitutionModal = new bootstrap.Modal(
						document.getElementById('substitutionModal')
					);
					substitutionModal.show();
				});
			});
		</script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	</body>
</html>
