<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';
require_once LIBRARY_PATH . '/exceptions.php';

$db = new Database();
$specialties = $db->getAllSpecialties();
usort($specialties, function ($a, $b) {
  return $a['name'] <=> $b['name'];
})
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Doctolibertain</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="public_html/css/index.css">
  <link rel="stylesheet" href="public_html/css/floating-square-animation.css">
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
  <main class="flex-grow-1 d-flex justify-content-center align-items-start">
    <form class="input-group mt-5" action="search.php" method="get" style="max-width: 70%;">
      <select class="form-select" name="spe">
        <option style="display:none;" value="" selected>Spécialité...</option>
        <?php foreach ($specialties as $spe) { ?>
          <option value="<?php echo $spe['id']; ?>"><?php echo $spe['name']; ?></option>
        <?php } ?>
      </select>
      <input type="text" class="form-control" placeholder="Nom du médecin" name="nom">
      <input type="text" class="form-control" placeholder="Où... (Code postal)" name="ou" pattern="\d{5}">
      <button class="btn btn-success" type="submit" id="search">Recherche</button>
    </form>
  </main>
</body>

</html>