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
	case 'doctors' . 'GET':
		$doctorsInfo = $db->getDoctorsAndSpecialties();
		http_response_code(200);
		die(json_encode($doctorsInfo));
		break;
	case 'specialties' . 'GET':
		$specialties = $db->getAllSpecialties();
		http_response_code(200);
		die(json_encode($specialties));
		break;
	case 'appointments' . 'GET':
		$appointments = $db->getAllFreeAppointments();

		if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			try {
				$authorization = getAuthorizationToken();
				$userInfos = $db->getUserInfos($authorization);
				$userId = $userInfos['id'];
				$userAppointments = $db->getUserAppointments($userId);
				$appointments = array_merge($appointments, $userAppointments);
			} catch (Exception | Error $_) {
			}
		}

		http_response_code(200);
		die(json_encode($appointments));
		break;
	case 'appointment' . 'PUT':
		$appointment_id = $_POST['id'];

		$authorization = getAuthorizationToken();
		try {
			$userInfos = $db->getUserInfos($authorization);
			$userId = $userInfos['id'];
			if (!$db->setAppointment($appointment_id, $userId)) {
				throw new Error();
			}
		} catch (AuthenticationException | Error $_) {
			APIErrors::invalidHeader();
		}

		http_response_code(200);
		die(json_encode(array(
			'message' => 'Appointment claimed successfully.'
		)));
		break;
	case 'appointment' . 'DELETE':
		$appointment_id = $_GET['id'];

		$authorization = getAuthorizationToken();
		if (!$db->verifyUserAccessToken($authorization)) {
			APIErrors::invalidHeader();
		}

		if (!$db->cancelAppointment($appointment_id)) {
			APIErrors::internalError();
		}

		http_response_code(200);
		die(json_encode(array(
			'message' => 'Appointment cancelled successfully.'
		)));
		break;
	default:
		http_response_code(404);
		die();
		break;
}
