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

<form method="post">
	<label for="emailLogin">Email</label>
	<input type="text" id="emailLogin" name="emailLogin" pattern=".{5,64}" required>
	<label for="passwordLogin">Password</label>
	<input type="password" id="passwordLogin" name="passwordLogin" pattern=".+" required>
	<button type="submit" name="login">Login</button>
</form>