<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if (array_key_exists('register', $_POST)) {
    $error_email = false;

    $firstname = $_POST['firstnameRegister'];
    $lastname = $_POST['lastnameRegister'];
    $email = $_POST['emailRegister'];
    $emailConf = $_POST['emailRegisterConf'];
    $phoneNumber = $_POST['phoneRegister'];
    $password = $_POST['passwordRegister'];

    if ($email == $emailConf) {
        try {
            $db->createUser($firstname, $lastname, $email, $phoneNumber, $password);
            $db->connectUser($email, $password);
            redirect('user.php');
        } catch (AuthenticationException $_) {
        } catch (DuplicateEmailException $_) {
        }
    } else {
        $error_email = true;
    }
}

if (array_key_exists('homepage', $_POST)) {
    redirect('');
}

if (array_key_exists('connection', $_POST)) {
    redirect('login.php');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>S'inscrire a DoctoLibertain</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fuggles&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="public_html/css/index.css" />
    <link rel="stylesheet" href="public_html/css/floating-square-animation.css">
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
    <?php
    if ($error_email) {
        echo "<div class=\"alert alert-danger\" role=\"alert\">";
        echo "L'email et l'email de confirmation sont différentes !!";
        echo "</div>";
    }
    ?>
    <div class="container-fluid d-flex justify-content-center">
        <div class="card my-4" style="width: 32rem;">
            <div class="card-body">
                <h5 class="card-title">Nouveau sur DoctoLibertain ?</h5>
                <h6 class="card-subtitle mb-2 text-muted">Saisissez vos informations pour continuer</h6>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Prenom" id="firstnameRegister" name="firstnameRegister" pattern=".{1,64}" required>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Nom" id="lastnameRegister" name="lastnameRegister" pattern=".{1,64}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Telephone portable (sinon fixe)" id="phoneRegister" name="phoneRegister" pattern=".{1,10}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Votre adresse mail" id="emailRegister" name="emailRegister" pattern=".{5,64}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Confirmez votre adresse mail" id="emailRegisterConf" name="emailRegisterConf" pattern=".{5,64}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="password" class="form-control" placeholder="Choisissez un mot de passe" id="passwordRegister" name="passwordRegister" pattern=".{8,256}" required>
                            <small id="passwordHelpInline" class="text-muted">
                                Doit contenir entre 8 et 256 caractères.
                            </small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-anim" name="register">M'inscrire</button>

                </form>
            </div>
        </div>
    </div>
</body>

</html>