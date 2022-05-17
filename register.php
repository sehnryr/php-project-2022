<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

if (isset($_POST['register'])) {
	$firstname = $_POST['firstnameLogin'];
	$lastname = $_POST['lastnameLogin'];
	$email = $_POST['emailLogin'];
	$phoneNumber = $_POST['phoneLogin'];
	$password = $_POST['passwordLogin'];


	try {
		$db->createUser($firstname, $lastname, $email, $phoneNumber, $password);
		$db->connectUser($email, $password);
		redirect('user.php');
	} catch (AuthenticationException $e) {
		return;
	} catch (DuplicateEmailException $e) {
		return;
	}
}
?>

<form method="post">
	<label for="firstnameLogin">First Name</label>
	<input type="text" id="firstnameLogin" name="firstnameLogin" pattern=".{1,64}" required>
	<label for="lastnameLogin">Last Name</label>
	<input type="text" id="lastnameLogin" name="lastnameLogin" pattern=".{1,64}" required>
	<label for="emailLogin">Email</label>
	<input type="text" id="emailLogin" name="emailLogin" pattern=".{5,64}" required>
	<label for="phoneLogin">Phone</label>
	<input type="text" id="phoneLogin" name="phoneLogin" pattern=".{1,10}" required>
	<label for="passwordLogin">Password</label>
	<input type="password" id="passwordLogin" name="passwordLogin" pattern=".+" required>
	<button type="submit" name="register">Register</button>
</form>