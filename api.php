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
			'error_description' => 'The request is missing the Authorization header.'
		)));
	}

	public static function invalidRequest()
	{
		http_response_code(400);
		die(json_encode(array(
			'error' => 'invalid_request',
			'error_description' => 'The request is missing a parameter, uses an unsupported parameter or repeats a parameter.'
		)));
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
		} catch (AccessTokenNotFound $_) {
			APIErrors::invalidGrant();
		}

		http_response_code(200);
		die(json_encode(array(
			'message' => 'Authorization code delete successfully.'
		)));
		break;
	case 'user' . 'GET':
		$authorization = getAuthorizationToken();

		try {
			$userInfos = $db->getUserInfos($authorization);
			http_response_code(200);
			die(json_encode($userInfos));
		} catch (AccessTokenNotFound $_) {
			APIErrors::invalidGrant();
		}
		break;
	default:
		http_response_code(404);
		die();
		break;
}
