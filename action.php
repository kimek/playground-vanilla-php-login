<?php
session_start();
require_once 'includes/json_helper.php';

if(empty($_POST)) {
	json_response(null,500);
}

if ($_POST['action'] === 'logout') {
	$_SESSION = array();
	session_destroy();
	json_response();
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
	die('CSRF token validation failed');
}

require_once 'db_connection.php';
require_once 'userSystem.php';

$userSystem = new UserSystem($pdo);

if ($_POST['action'] === 'register') {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$file = $_FILES['file'];

	try {
		$result = $userSystem->register($username, $password, $file);
		if($result) {
			json_response('Registration successful!');
		}

		json_response('Registration failed, please try again');
	} catch (RuntimeException $e) {
		json_response('Registration failed! Please try again.', 500);
		error_log('raytrace-id ' . $e->getMessage());
	}
}

if ($_POST['action'] === 'login') {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($userSystem->login($username, $password)) {
		json_response();
	} else {
		json_response('Invalid credentials!',500);
	}
}

json_response('',500);
