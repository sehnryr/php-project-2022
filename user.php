<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if(array_key_exists('homepage', $_POST)){
	redirect('');
}

// bind button 'disconnect' to this condition.
if (array_key_exists('disconnect', $_POST)) {
	$db->disconnectUser();
	redirect('');
}

$access_token = $_COOKIE['docto_session'];

try {
	$infos = $db->getUserInfos($access_token);
} catch (Exception $_) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
        	<form method="post">
    	    	<button class="navbar-brand bg-transparent border-0" name="homepage" id="homepage" style="font-family: 'Fuggles', cursive;font-size: 45px;">
				    DoctoLibertain
		    	</button>
		    	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
		    	aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      			    <span class="navbar-toggler-icon"></span>
    	    	</button>
	    	</form>
        	<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          		<div class="nav-item">
		  			<h4 class="text-white"><?php echo $infos['firstname']; ?> <?php echo $infos['lastname']; ?></h4>
          		</div>
          		<div class="nav-item">
            		<form method="post">
               		 	<button class="nav-link container-fluid bg-transparent border-0" name="disconnect">
                    		<ul class="list-inline">
								<li class="list-inline-item"><img src="public_html/img/power_settings_new_FILL0_wght400_GRAD0_opsz48.svg" alt="déconnection"></li>
								<li class="list-inline-item"><p class="text-white">Déconnexion</p></li>
							</ul>
                		</button>
            		</form>
				</div>
          	</div>
        </div>
    </nav>
	<div class="container-fluid">
		<div class="row">
			<div class="col-3  d-flex justify-content-center" style="background-color: #C7D0D9; height: 89.80vh">
				<h5 class="mt-4 text-decoration-underline">Voir les rendez-vous passés :</h5>
			</div>
			<div class="col d-flex align-items-center justify-content-center" style="background-color: #E9EDF1">
				<div>
					<img src="public_html/img/calendar_add_on_FILL0_wght400_GRAD0_opsz48.svg" alt="calendar" style="transform: rotate(-14.17deg);">
					<p>
						Aucun rendez-vous à venir<br>
						Prendre rendez-vous
					</p>
				</div>
			</div>
		</div>
	</div>
</body>
</html>