<?php
session_start();
require_once __DIR__ . '/../src/inc/json_helper.php';
require_once __DIR__ . '/../src/inc/csrf_helper.php';
require_once __DIR__ . '/../src/inc/db_connection.php';
require_once __DIR__ . '/../src/controllers/userSystem.php';

if (empty($_POST)) {
	json_response('No data received', 400);
}

$action = $_POST['action'] ?? '';

if ($action !== 'logout') {
	validate_csrf_token();
}

$userSystem = new UserSystem($pdo);

switch ($action) {
	case 'logout':
		$_SESSION = array();
		session_destroy();
		json_response('Logged out successfully');
		break;

	case 'register':
		$username = trim($_POST['username']);
		$password = $_POST['password'];
		$file = $_FILES['file'];

		if (empty($username) || empty($password) || empty($file)) {
			json_response('All fields are required', 400);
		}

		$result = $userSystem->register($username, $password, $file);
		if (isset($result['error'])) {
			json_response($result['error'], 500);
		}
		json_response($result['success']);
		break;

	case 'login':
		$username = trim($_POST['username']);
		$password = $_POST['password'];

		if (empty($username) || empty($password)) {
			json_response('Username and password are required', 400);
		}

		if ($userSystem->login($username, $password)) {
			json_response('Login successful');
		} else {
			json_response('Invalid credentials!', 401);
		}
		break;

	default:
		json_response('Invalid action', 400);
		break;
}
