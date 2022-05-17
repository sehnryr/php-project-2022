<?php

require_once 'php/database.php';

$db = new Database();

if (isset($_COOKIE['docto_session'])) {
	$db->disconnectUser('cm@test.com', $_COOKIE['docto_session']);
}

// var_dump($db->disconnectUser('cm@test.com', '1234'));
