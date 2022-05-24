<?php
require_once 'resources/config.php';
require_once 'resources/database.php';
require_once LIBRARY_PATH . '/common.php';
require_once LIBRARY_PATH . '/exceptions.php';

$pathInfo = explode('/', trim($_SERVER['PATH_INFO'], '/\\'));

header('content-type: application/json; charset=utf-8');

$db = new Database();

function getAuthorizationToken(): ?string
{
	$authorization = $_SERVER['HTTP_AUTHORIZATION'];

	if (!isset($authorization)) {
		APIErrors::invalidHeader();
	}

	$authorization = explode(' ', trim($authorization), 2)[1];

	if (empty($authorization)) {
		APIErrors::invalidGrant();
	}

	return $authorization;
}

class APIErrors
{
	public static function invalidGrant()
	{
		http_response_code(400);
		die(json_encode(array(
			'error' => 'invalid_grant',
			'error_description' => 'The authorization code is invalid or expired.'
		)));
	}

	public static function invalidHeader()
	{
		http_response_code(400);
		die(json_encode(array(
			'error' => 'invalid_header',
			'error_description' => 'The request is missing the Authorization header or the Authorization header is invalid.'
		)));
	}

	public static function invalidRequest()
	{
		http_response_code(400);
		die(json_encode(array(
			'error' => 'invalid_request',
			'error_description' => 'The request is missing a parameter, uses an unsupported parameter, uses an invalid parameter or repeats a parameter.'
		)));
	}

	public static function internalError()
	{
		http_response_code(500);
		die();
	}
}

switch ($pathInfo[0] . $_SERVER['REQUEST_METHOD']) {
	case 'login' . 'POST':
		$email = $_POST['email'];
		$password = $_POST['password'];

		if (!isset($email) || !isset($password)) {
			APIErrors::invalidRequest();
		}

		$access_token = $db->getUserAccessToken($email, $password);

		if (empty($access_token)) {
			APIErrors::invalidRequest();
		}

		http_response_code(200);
		die(json_encode(array(
			'access_token' => $access_token,
			'created_at' => time(),
			'token_type' => 'bearer'
		)));
		break;
	case 'logout' . 'POST':
		$authorization = getAuthorizationToken();

		try {
			$db->removeUserAccessToken($authorization);
		} catch (AuthenticationException $_) {
			APIErrors::invalidGrant();
		}

		http_response_code(200);
		die(json_encode(array(
			'message' => 'Authorization code delete successfully.'
		)));
		break;
	case 'register' . 'POST':
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$phoneNumber = $_POST['phone'];
		$password = $_POST['password'];

		try {
			$db->createUser($firstname, $lastname, $email, $phoneNumber, $password);
		} catch (Exception $_) {
			APIErrors::invalidRequest();
		}

		$access_token = $db->getUserAccessToken($email, $password);

		http_response_code(200);
		die(json_encode(array(
			'access_token' => $access_token,
			'created_at' => time(),
			'token_type' => 'bearer'
		)));
		break;
	case 'delete' . 'DELETE':
		$authorization = getAuthorizationToken();

		try {
			$db->deleteUserWithToken($authorization);
		} catch (AuthenticationException $_) {
			APIErrors::invalidHeader();
		}

		http_response_code(200);
		die(json_encode(array(
			'message' => 'User deleted successfully.'
		)));
		break;
	case 'user' . 'GET':
		$authorization = getAuthorizationToken();

		try {
			$userInfos = $db->getUserInfos($authorization);
			http_response_code(200);
			die(json_encode($userInfos));
		} catch (AuthenticationException $_) {
			APIErrors::invalidGrant();
		}
		break;
	case 'specialties' . 'GET':
		$specialities = $db->getAllSpecialties();
		http_response_code(200);
		die(json_encode($specialities));
		break;
	default:
		http_response_code(404);
		die();
		break;
}
