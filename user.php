<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';

$db = new Database();

// bind button 'disconnect' to this condition.
if (array_key_exists('disconnect', $_POST)) {
	$db->disconnectUser();
	redirect('login.php');
}

$infos = $db->getUserInfo();

if (!$infos) {
	redirect('login.php');
}

?>

<h1>Hello <?php echo $infos['firstname']; ?> <?php echo $infos['lastname']; ?>!</h1>
<p>Your email is: <i><?php echo $infos['email']; ?></i>,
	and your phone number: <i><?php echo $infos['phone_number']; ?></i></p>

<form method="post">
	<input type="submit" name="disconnect" value="Disconnect">
</form>