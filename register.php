<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if (isset($_POST['register'])) {
	$firstname = $_POST['firstnameRegister'];
	$lastname = $_POST['lastnameRegister'];
	$email = $_POST['emailRegister'];
	$phoneNumber = $_POST['phoneRegister'];
	$password = $_POST['passwordRegister'];


	try {
		$db->createUser($firstname, $lastname, $email, $phoneNumber, $password);
		$db->connectUser($email, $password);
		redirect('user.php');
	} catch (AuthenticationException $_) {
	} catch (DuplicateEmailException $_) {
	}
}

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
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body style="background-color: #EAF7FD" >
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
                <button class="nav-link container-fluid bg-transparent border-0" name="connection">
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

<div class="container-fluid d-flex justify-content-center">
    <div class="card my-4" style="width: 32rem;">
        <div class="card-body">
            <p>Nouveau sur DoctoLibertain ?</p>
            <p>Saisissez vos informations pour continuer</p>
            <form>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Prenom" 
						id="firstnameRegister" name="firstnameRegister" pattern=".{1,64}" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Nom"
						id="lastnameRegister" name="lastnameRegister" pattern=".{1,64}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Telephone portable (sinon fixe)"
						id="phoneRegister" name="phoneRegister" pattern=".{1,10}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Votre adresse mail"
						id="emailRegister" name="emailRegister" pattern=".{5,64}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Confirmez votre adresse mail"
						id="emailRegisterConf" name="emailRegisterConf" pattern=".{5,64}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="password" class="form-control" placeholder="Choisissez un mot de passe"
						id="passwordRegister" name="passwordRegister" pattern=".+" required>
                    </div>
                </div>   
                    
                <button type="submit" class="btn btn-primary" name="register">S'INSCRIRE</button>

            </form>
        </div>
    </div>
</div>
</body>
</html> 