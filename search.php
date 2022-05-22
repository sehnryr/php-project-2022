<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'resources/config.php';
    require_once 'resources/database.php';
    require_once LIBRARY_PATH . '/common.php';
    require_once LIBRARY_PATH . '/exceptions.php';

    if(array_key_exists('homepage', $_POST)){
        redirect('');
    }
    
    if(array_key_exists('connection', $_POST)){
        redirect('login.php');
    }
    
    $db = new Database();
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
    <nav class="navbar navbar-expand-lg" style="background-color: #107ACA;">
        <div class="container-fluid">
        <form method="post">
    	    <button class="navbar-brand bg-transparent border-0 text-white" name="homepage" id="homepage" style="font-family: 'Fuggles', cursive;font-size: 45px;">
			    DoctoLibertain
		    </button>
		    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
		    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      		    <span class="navbar-toggler-icon"></span>
    	    </button>
	    </form>
          <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item mt-2">
                <a class="nav-link" href="#">
                    <div style="font-size: 15px;color: #107ACA;" class="badge rounded-pill bg-light">
                        Vous êtes un professionnel de santé ?
                    </div>
                </a>
              </li>
              <li class="nav-item">
                <form method="post">
                    <button class="nav-link container-fluid bg-transparent border-0 text-white" name="connection">
                      <ul class="list-inline">
                        <li class="list-inline-item mb-2">
                          <img src="public_html/img/person_FILL1_wght400_GRAD0_opsz48.svg" alt="Person" style="transform: translate(0, -1.25vh);">
                        </li>
                        <li class="list-inline-item">
                          <div class="fw-bolder link-light">
                            Se connecter
                          </div>
                          <div style="opacity: 0.7; font-size: 12px;">
                            Gérer mes rdv
                          </div>
                        </li>
                      </ul>
                    </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
    </nav>
    <!--barre de recherche + image-->
    <div class="d-flex align-items-center justify-content-center position-relative" style="height: 8rem; background-image: linear-gradient(#107ACA, #10A7DA); display: grid; grid-template-rows: auto auto auto; grid-template-columns: auto auto auto;"> 
        <form action="search.php" method="get" style="display: flex; align-items: center; justify-content: center;">
            <select name="spe" class="form-control" required>
                <option value="">Quelle spécialité ?</option>
                <?php
                  $spes = $db->getAllSpecialties();
                  foreach ($spes as $spe) {
                    echo "<option value=\"" . $spe['id'] . "\">" . $spe['name'] . "</option>";
                  }
                ?>
            </select>
            <input type="text" class="form-control" placeholder="Où" name="ou">
            <button style="background-color:orange;" type="submit" class="btn btn-primary">Recherche</button>
        </form>
    </div>
    <!-- Résultat de recherche -->
    <?php
        $appoints = $db->getAppointmentsForASpecialty($_GET['spe']);
        foreach ($appoints as $appoint) {
            echo "<div class=\"card\" style=\"width: 18rem;\">";
            echo "<div class=\"card-body\">";
            echo "<h5 class=\"card-title\">". $appoint['firstname'] . " " . $appoint['lastname'] ."</h5>";
            echo "<h6 class=\"card-subtitle mb-2 text-muted\">". $appoint['name'] ."</h6>";
            echo "<p class=\"card-text\">". $appoint['date_time'] ."</p>";
            echo "</div>";
            echo "</div>";
        }
    ?>
</body>
</html>