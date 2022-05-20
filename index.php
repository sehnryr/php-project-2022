<?php

    require_once 'resources/config.php';
    require_once LIBRARY_PATH . '/common.php';

    if(array_key_exists('homepage', $_POST)){
        redirect('');
    }
    
    if(array_key_exists('connection', $_POST)){
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
              <li class="nav-item">
                <a class="nav-link" href="#">
                    <div style="font-size: 15px;" class="badge rounded-pill bg-light text-dark">
                        Vous êtes un professionnel de santé ?
                    </div>
                </a>
              </li>
              <div class="nav-item">
                <img  width="16" height="16" src="public_html/img/person_FILL1_wght400_GRAD0_opsz48.svg" alt="Person">
              </div>
              <li class="nav-item">
                <form method="post">
                    <button class="nav-link container-fluid bg-transparent border-0 text-white" name="connection">
                        <div class="">
                            <div class="fw-bolder link-light">
                                Se connecter
                            </div>
                            <div style="opacity: 0.7; font-size: 12px;">
                                Gérer mes rdv
                            </div>
                        </div>
                    </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!--barre de recherche + image-->
      <div class="d-flex align-items-center justify-content-center position-relative" style="height: 20rem; background-image: linear-gradient(#107ACA, #10A7DA); display: grid; grid-template-rows: auto auto auto; grid-template-columns: auto auto auto;"> 
        <form style="display: flex; align-items: center; justify-content: center;">
          <input type="text" class="form-control" placeholder="Spécialité">
          <input type="text" class="form-control" placeholder="Où">
          <button style="background-color:orange;" type="submit" class="btn btn-primary">Recherche</button>
        </form>
        <img class="position-absolute bottom-0 end-0" style="resize: both; width: 20vw; height: 20vh;" src="public_html/img/raoul.png">
      </div>
</body>
</html>