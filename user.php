<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if (array_key_exists('homepage', $_POST)) {
	redirect('index.php');
}

if (array_key_exists('search', $_POST)) {
	redirect('search.php');
}

// bind button 'disconnect' to this condition.
if (array_key_exists('disconnect', $_POST)) {
	$db->disconnectUser();
	redirect('index.php');
}


try {
	$access_token = $_COOKIE['docto_session'];
	$infos = $db->getUserInfos($access_token);
} catch (Exception | Error $_) {
	redirect('login.php');
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<title>Doctolibertain</title>
	<link rel="stylesheet" href="public_html/css/index.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<div class="container-fluid">
			<form method="post">
				<button class="navbar-brand bg-transparent border-0" name="homepage" id="homepage" style="font-family: 'Fuggles', cursive;font-size: 45px;">
					DoctoLibertain
				</button>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</form>
			<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
				<div class="nav-item">
					<h4 class="text-white"><?php echo $infos['firstname']; ?> <?php echo $infos['lastname']; ?></h4>
				</div>
				<div class="nav-item mt-3	">
					<form method="post">
						<button class="nav-link container-fluid bg-transparent border-0" name="disconnect">
							<ul class="list-inline">
								<li class="list-inline-item"><img src="public_html/img/power_settings_new_FILL0_wght400_GRAD0_opsz48.svg" alt="déconnection"></li>
								<li class="list-inline-item">
									<p class="text-white">Déconnexion</p>
								</li>
							</ul>
						</button>
					</form>
				</div>
			</div>
		</div>
	</nav>
	<div class="container-fluid">
		<div class="row">
			<div class="col-3 d-flex flex-column align-items-start justify-content-center" style="background-color: #C7D0D9; height: 88.2vh">
				<h5 class="mt-4 text-decoration-underline">Voir les rendez-vous passés :</h5>
				<div class="col mt-2">
					<?php
					$put_an_appointment = false;
					$appointements = $db->getAllAppointments($infos['id']);
					if (!empty($appointements)) {
						foreach ($appointements as $appointement) {
							if (strtotime($appointement['date_time']) < time()) {
								$put_an_appointment = true;
								$date = explode(" ", $appointement['date_time']);
								$doc = $db->getDoctorName($appointement['doctorid']);

								echo "<div class=\"card mt-2\" style=\"width: 18rem;\">";
								echo "<div class=\"card-body\">";
								echo "<h5 class=\"card-title\"><span class=\"badge rounded-pill text-black\" style=\"background-color: #C4C4C4;\">";
								echo "<img src=\"public_html/img/calendar_month_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\">";
								echo date("d M Y", strtotime($date[0]));
								echo "<img src=\"public_html/img/schedule_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"clock\">";
								echo $date[1];
								echo "</span></h5>";
								echo "<p class=\"card-text\">";
								echo "Dr. " . $doc['firstname'] . " " . $doc['lastname'];
								echo "</p>";
								echo "<p class=\"card-text\">";
								echo "Spécialté : " . $db->getDoctorSpecialty($appointement['doctorid']);
								echo "</p>";
								echo "</div>";
								echo "</div>";
							}
						}
						if (!$put_an_appointment) {
							echo "<p>Vous n'avez pas pris d'ancien rendez-vous avec DoctoLibertain</p>";
						}
					} else {
						echo "<p>Vous n'avez pas pris d'ancien rendez-vous avec DoctoLibertain</p>";
					}
					?>
				</div>
			</div>
			<div class="col d-flex align-items-center justify-content-center" style="background-color: #E9EDF1">
				<div>
					<?php
					$put_an_appointment = false;
					if (!empty($appointements)) {
						foreach ($appointements as $appointement) {
							if (strtotime($appointement['date_time']) > time()) {
								$put_an_appointment = true;
								$date = explode(" ", $appointement['date_time']);
								$doc = $db->getDoctorName($appointement['doctorid']);

								echo "<div class=\"card mt-2\" style=\"width: 18rem;\">";
								echo "<div class=\"card-body\">";
								echo "<h5 class=\"card-title\"><span class=\"badge rounded-pill text-black\" style=\"background-color: #C4C4C4;\">";
								echo "<img src=\"public_html/img/calendar_month_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\">";
								echo date("d M Y", strtotime($date[0]));
								echo "<img src=\"public_html/img/schedule_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"clock\">";
								echo $date[1];
								echo "</span></h5>";
								echo "<p class=\"card-text\">";
								echo "Dr. " . $doc['firstname'] . " " . $doc['lastname'];
								echo "</p>";
								echo "<p class=\"card-text\">";
								echo "Spécialté : " . $db->getDoctorSpecialty($appointement['doctorid']);
								echo "</p>";
								echo "</div>";
								echo "</div>";
							}
						}
						if (!$put_an_appointment) {
							echo "<ul class=\"list-inline\"><li class=\"list-inline-item\">";
							echo "<img src=\"public_html/img/calendar_add_on_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\" style=\"transform: rotate(-14.17deg) translate(0, -2vh);;\">";
							echo "</li><li class=\"list-inline-item\"><p>";
							echo "Aucun rendez-vous à venir<br>";
							echo "<form method=\"post\">";
							echo "<button class=\"navbar-brand bg-transparent border-0 m-0 p-0 text-primary\" name=\"search\" style=\"font-size: 15px\">";
							echo "Prendre un nouveau rendez-vous";
							echo "</button>";
							echo "</form>";
							echo "</p></li></ul>";
						}
					} else {
						echo "<img src=\"public_html/img/calendar_add_on_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\" style=\"transform: rotate(-14.17deg);\">";
						echo "<p>";
						echo "Aucun rendez-vous à venir<br>";
						echo "<form method=\"post\">";
						echo "<button class=\"navbar-brand bg-transparent border-0 m-0 p-0 text-primary\" name=\"search\" style=\"font-size: 15px\">";
						echo "Prendre un nouveau rendez-vous";
						echo "</button>";
						echo "</form>";
						echo "</p>";
					}
					?>
				</div>
			</div>
		</div>
	</div>
</body>

</html>