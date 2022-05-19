<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';
require_once LIBRARY_PATH . '/exceptions.php';

$db = new Database();

if (isset($_COOKIE['docto_session'])) {
	try {
		$db->tryConnectUser();
		redirect('user.php');
	} catch (AuthenticationException $e) {
	}
}

if(array_key_exists('homepage', $_POST)){
	redirect('');
}

if(array_key_exists('connection', $_POST)){
	redirect('login.php');
}

if (isset($_POST['login'])) {
	$email = $_POST['emailLogin'];
	$password = $_POST['passwordLogin'];

	try {
		$db->connectUser($email, $password);
		redirect('user.php');
	} catch (AuthenticationException $e) {
	}
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
      <form method="post">
        <p>J'ai deja un compte DoctoLibertain</p>
        <div class="form-group">
          <input id="emailLogin" name="emailLogin" type="text" class="form-control mx-sm-3" placeholder="Email" pattern=".{5,64}" required>
        </div>
        <div class="form-group">
          <input type="password" id="passwordLogin" name="passwordLogin" class="form-control mx-sm-3" aria-describedby="passwordHelpInline" placeholder="Mot de passe" pattern=".+" required>
          <small id="passwordHelpInline" class="text-muted">
            Must be 8-20 characters long.
          </small>
        </div>
  
        <div class="custom-control custom-checkbox mb-3">
          <input type="checkbox" class="custom-control-input" id="customControlValidation1">
          <label class="custom-control-label" for="customControlValidation1">Se souvenir de mon identifiant</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary">SE CONNECTER</button>
      </form>
    </div>
  </div>
</div>
<div class="container-fluid d-flex justify-content-center">
  <div class="card my-4" style="width: 32rem;">
    <div class="card-body">
      <p>Nouveau sur DoctoLibertain ?</p>
      <a href="DoctoInscription.html" style="background-color: #2E96E2;">S'INSCRIRE</a>
    </div>
  </div>
</div>
</body>
</html>