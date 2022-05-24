<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if (array_key_exists('search', $_POST)) {
	redirect('search.php');
}

if (array_key_exists('reTakeRDV', $_POST)) {
	redirect('search.php?spe=' . $_POST['spe'] . "&nom=" . $_POST['nom'] . "&ou=" . $_POST['ou']);
}

// bind button 'disconnect' to this condition.
if (array_key_exists('disconnect', $_POST)) {
	$db->disconnectUser();
	redirect('index.php');
}

if (array_key_exists('cancelAppointment', $_POST)) {
	$db->cancelAppointment($_POST['cancelAppointment']);
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
	<meta charset="UTF-8" />
	<title><?php echo $infos['firstname'] . " " . $infos['lastname']; ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="public_html/css/index.css" />
	<link rel="stylesheet" href="public_html/css/floating-square-animation.css" />
	<link rel="icon" type="image/png" href="public_html/img/raoul.png"/> 
</head>

<body class="d-flex flex-column">
	<div class="area">
		<ul class="circles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
	<nav class="navbar navbar-expand-lg">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.php">DoctoLibertain</a>
			<div class="d-flex justify-content-end align-items-center">
				<div class="text-white fs-3 me-2"><?php echo $infos['firstname'] . " " . $infos['lastname']; ?></div>
				<form method="POST">
					<button class="btn btn-outline-light ms-2" name="disconnect">
						<div class="d-flex align-items-center">
							<img class="me-2" style="height: 2rem;" src="public_html/img/power_settings_new_FILL0_wght400_GRAD0_opsz48.svg">
							<div class="d-flex flex-column align-items-start">
								<span>Déconnexion</span>
							</div>
						</div>
					</button>
				</form>
			</div>
		</div>
	</nav>
	<main class="flex-grow-1 row">
		<!-- Rendez-vous passé -->
		<div class="col-3 ms-1 d-flex flex-column align-items-start justify-content-center" style="background-color: #C7D0D9; width:22em;">
			<h5 class="mt-4 text-decoration-underline" style="margin: 1.5em;"> Voir les rendez-vous passés :</h5>
			<div class="col mt-2">
				<?php
				$put_an_appointment = false;
				$appointments = $db->getUserAppointments($infos['id']);
				if (!empty($appointments)) {
					foreach ($appointments as $appointment) {
						if (strtotime($appointment['date_time']) < time()) {
							$put_an_appointment = true;
							$date = explode(" ", $appointment['date_time']);
							$pcode = $db->getDoctorPCode($appointment['doctor_id']);

							echo "<div class=\"card mt-2\" style=\"width: auto; margin:1em\">";
							echo "<div class=\"card-body\">";
							echo "<h5 class=\"card-title\"><span class=\"badge rounded-pill text-black\" style=\"background-color: #C4C4C4;\">";
							echo "<img src=\"public_html/img/calendar_month_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\">";
							echo date("d M Y", strtotime($date[0]));
							echo "<img src=\"public_html/img/schedule_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"clock\">";
							echo $date[1];
							echo "</span></h5>";
							echo "<p class=\"card-text\">";
							echo "Dr. " . $appointment['firstname'] . " " . $appointment['lastname'];
							echo "</p>";
							echo "<p class=\"card-text\">";
							echo "Spécialté : " . $appointment['specialty_name'];
							echo "</p>";
							echo "<form method=\"post\">";
							echo "<input type=\"text\" name=\"spe\" value=\"" . $appointment['specialty_id'] . "\" hidden>";
							echo "<input type=\"text\" name=\"nom\" value=\"" . $appointment['firstname'] . " " . $appointment['lastname'] . "\" hidden>";
							echo "<input type=\"text\" name=\"ou\" value=\"" . $pcode . "\" hidden>";
							echo "<button class=\" border-0\" style=\"border-radius:5px; width:10em; background-color: #F6C844;\" name=\"reTakeRDV\">";
							echo "  Reprendre un RDV ?  ";
							echo "</button>";
							echo "</form>";
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
		<!-- Rendez-vous à venir -->
		<div class="col d-flex align-items-center justify-content-center" style="background-color: #E9EDF1">
			<div>
				<?php
				$put_an_appointment = false;
				if (!empty($appointments)) {
					foreach ($appointments as $appointment) {
						if (strtotime($appointment['date_time']) > time()) {
							$put_an_appointment = true;
							$date = explode(" ", $appointment['date_time']);

							echo "<div class=\"card mt-2\" style=\"width: 18rem;\">";
							echo "<div class=\"card-body\">";
							echo "<h5 class=\"card-title\"><span class=\"badge rounded-pill text-black\" style=\"background-color: #C4C4C4;\">";
							echo "<img src=\"public_html/img/calendar_month_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\">";
							echo date("d M Y", strtotime($date[0]));
							echo "<img src=\"public_html/img/schedule_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"clock\">";
							echo $date[1];
							echo "</span></h5>";
							echo "<p class=\"card-text\">";
							echo "Dr. " . $appointment['firstname'] . " " . $appointment['lastname'];
							echo "</p>";
							echo "<p class=\"card-text\">";
							echo "Spécialté : " . $appointment['specialty_name'];
							echo "</p>";
							echo "<form method=\"post\">";
							echo "<button class=\"bg-danger text-white border-0\" name=\"cancelAppointment\" style=\"transform: translate(7vw)\" value=\"" . $appointment['id'] . "\">";
							echo "Annuler le rdv ?";
							echo "</button>";
							echo "</form>";
							echo "</div>";
							echo "</div>";
						}
					}
					if (!$put_an_appointment) {
						echo "<ul class=\"list-inline\"><li class=\"list-inline-item\">";
						echo "<img src=\"public_html/img/calendar_add_on_FILL0_wght400_GRAD0_opsz48.svg\" alt=\"calendar\" style=\"transform: rotate(-14.17deg) translate(0, -2vh);;\">";
						echo "</li><li class=\"list-inline-item\"><p style=\"font-size:20px\">";
						echo "Aucun rendez-vous à venir<br>";
						echo "<form method=\"post\">";
						echo "<button class=\"navbar-brand bg-transparent border-0 m-0 p-0 text-primary\" name=\"search\" style=\"font-size: 35px\">";
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
	</main>
</body>

</html>