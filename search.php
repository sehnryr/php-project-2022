<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';
require_once LIBRARY_PATH . '/exceptions.php';

$db = new Database();

if (array_key_exists('setAppointment', $_POST)) {
  try {
    $access_token = $_COOKIE['docto_session'];
    $infos = $db->getUserInfos($access_token);
    $db->setAppointment($_POST['setAppointment'], $infos['id']);
  } catch (Exception | Error $_) {
    redirect('login.php');
  }
}

$specialties = $db->getAllSpecialties();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Doctolibertain</title>
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
    <!--barre de recherche + image-->
    <form class="input-group my-5" action="search.php" method="get" style="max-width: 70%;">
      <select class="form-select" name="spe">
        <option style="display:none;" value="" <?php if(!empty($_GET['spe'])) echo 'disabled'; ?>>Spécialité...</option>
        <?php foreach ($specialties as $spe) { ?>
          <option value="<?php echo $spe['id']; ?>" <?php if ($spe['id'] == $_GET['spe']) echo 'selected'; ?>><?php echo $spe['name']; ?></option>
        <?php } ?>
      </select>
      <input type="text" class="form-control" placeholder="Nom du médecin" name="nom" <?php if (isset($_GET['nom'])) echo 'value="' . $_GET['nom'] . '"'; ?>>
      <input type="text" class="form-control" placeholder="Où... (Code postal)" name="ou" pattern="\d{5}" <?php if (isset($_GET['ou'])) echo 'value="' . $_GET['ou'] . '"'; ?>>
      <button class="btn btn-success" type="submit" id="search">Recherche</button>
    </form>
    <!-- Résultat de recherche -->
    <div class="d-flex justify-content-center align-content-start flex-wrap">
      <?php
      if(empty($_GET['spe']) && empty($_GET['nom'])){
        redirect('index.php');
      }
      $put_a_value = false;
      if(!empty($_GET['spe']) && !empty($_GET['nom'])){
        $appoints = $db->getAppointmentsForASpecialty($_GET['spe']);
          foreach ($appoints as $appoint) {
            if(strtotime($appoint['date_time']) <= time()) continue;
            $metinput = metaphone($_GET['nom']);
            $metappoint = metaphone($appoint['firstname']. " " . $appoint['lastname']);
            if($metinput == $metappoint || stristr($metappoint, $metinput)){
              if(!empty($_GET['ou'])){
                if ($db->getDoctorPCode($appoint['doctor_id']) == $_GET['ou']) {  ?>
                  <div class="card m-1" style="width: 18rem;">
                    <div class="card-body">
                      <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                      <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                      <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                      <form method="POST">
                        <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                      </form>
                    </div>
                  </div>
                  <?php
                    $put_a_value = true;
                  }
                }else{ ?>
                  <div class="card m-1" style="width: 18rem;">
                    <div class="card-body">
                      <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                      <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                      <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                      <form method="POST">
                        <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                      </form>
                    </div>
                  </div>
                  <?php
                  $put_a_value = true;
              }
            }
          }
      }else{
        if(!empty($_GET['spe'])){
          $appoints = $db->getAppointmentsForASpecialty($_GET['spe']);
          foreach ($appoints as $appoint) {
            if(strtotime($appoint['date_time']) <= time()) continue;
            if(!empty($_GET['ou'])){
              if ($db->getDoctorPCode($appoint['doctor_id']) == $_GET['ou']) {  ?>
                <div class="card m-1" style="width: 18rem;">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                    <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                    <form method="POST">
                      <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                    </form>
                  </div>
                </div>
                <?php
                  $put_a_value = true;
                }
              }else{ ?>
                <div class="card m-1" style="width: 18rem;">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                    <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                    <form method="POST">
                      <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                    </form>
                  </div>
                </div>
                <?php
                $put_a_value = true;
            }
          }
        }else{ //not empty get nom
          $appoints = $db->getAllFreeAppointments();
          foreach ($appoints as $appoint) {
            if(strtotime($appoint['date_time']) <= time()) continue;
            $metinput = metaphone($_GET['nom']);
            $metappoint = metaphone($appoint['firstname']. " " . $appoint['lastname']);
            if($metinput == $metappoint || stristr($metappoint, $metinput)){
              if(!empty($_GET['ou'])){
                if ($db->getDoctorPCode($appoint['doctor_id']) == $_GET['ou']) {  ?>
                <div class="card m-1" style="width: 18rem;">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                    <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                    <form method="POST">
                      <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                    </form>
                  </div>
                </div>
                <?php
                  $put_a_value = true;
                }
              }else{ ?>
                <div class="card m-1" style="width: 18rem;">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $appoint['firstname'] . " " . $appoint['lastname']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $appoint['specialty_name']; ?></h6>
                    <p class="card-text"><?php echo $appoint['date_time']; ?></p>
                    <form method="POST">
                      <button class="btn btn-primary" name="setAppointment" value="<?php echo $appoint['id']; ?>">Réserver le rdv</button>
                    </form>
                  </div>
                </div>
                <?php
                $put_a_value = true;
              }
            }
          }
        }
      }
      if (!$put_a_value) {
        echo "<div class=\"alert alert-warning\" role=\"alert\">";
        echo "Il n'y a pas de médecin pour vos critères dans la base de données !";
        echo "</div>";
      }
      ?>
    </div>
  </main>
</body>

</html>