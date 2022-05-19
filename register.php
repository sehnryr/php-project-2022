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
?>

<form method="post">
	<label for="firstnameRegister">First Name</label>
	<input type="text" id="firstnameRegister" name="firstnameRegister" pattern=".{1,64}" required>
	<label for="lastnameRegister">Last Name</label>
	<input type="text" id="lastnameRegister" name="lastnameRegister" pattern=".{1,64}" required>
	<label for="emailRegister">Email</label>
	<input type="text" id="emailRegister" name="emailRegister" pattern=".{5,64}" required>
	<label for="phoneRegister">Phone</label>
	<input type="text" id="phoneRegister" name="phoneRegister" pattern=".{1,10}" required>
	<label for="passwordRegister">Password</label>
	<input type="password" id="passwordRegister" name="passwordRegister" pattern=".+" required>
	<button type="submit" name="register">Register</button>
</form>