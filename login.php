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

if (array_key_exists('register', $_POST)) {
  redirect('register.php');
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
  <meta charset="UTF-8" />
  <title>Se connecter à DoctoLibertain</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="public_html/css/index.css" />
  <link rel="stylesheet" href="public_html/css/floating-square-animation.css" />
  <link rel="icon" type="image/png" href="public_html/img/raoul.png"/> 
  <link rel="stylesheet" href="public_html/css/anim-button.css" />
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
    <img class="home-image" src="public_html/img/TaeAugust07.svg" />
  </div>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">DoctoLibertain</a>
      <div class="d-flex justify-content-end align-items-center">
        <button class="btn btn-light" id="btn-pro" disabled>
          Vous êtes un professionnel de santé ?
        </button>
        <a class="btn btn-outline-light ms-2" href="login.php">
          <div class="d-flex align-items-center">
            <img class="me-2" style="height: 2rem;" src="public_html/img/person_FILL1_wght400_GRAD0_opsz48.svg">
            <div class="d-flex flex-column align-items-start">
              <span>Se connecter</span>
              <small>Gérer mes rdv</small>
            </div>
          </div>
        </a>
      </div>
    </div>
  </nav>
  <main class="flex-grow-1 d-flex flex-column align-items-center">
    <div class="card my-4" style="width: 30rem">
      <div class="card-body">
        <h5 class="card-title">J'ai deja un compte DoctoLibertain</h5>
        <form method="post">
          <input id="emailLogin" name="emailLogin" type="text" class="form-control my-3" placeholder="Email" pattern=".{5,64}" required />
          <input type="password" id="passwordLogin" name="passwordLogin" class="form-control my-3" aria-describedby="passwordHelpInline" placeholder="Mot de passe" pattern=".{1,256}" required />
          <button type="submit" name="login" class="btn btn-primary btn-anim">Me connecter</button>
        </form>
      </div>
    </div>
    <div class="card mb-4" style="width: 12rem">
      <div class="card-body">
        <h5 class="card-title">Pas de compte ?</h5>
        <a class="btn btn-primary btn-anim" href="register.php">M'inscrire</a>
      </div>
    </div>
  </main>
</body>

</html>