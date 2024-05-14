<?php
session_start();
require_once 'config.php';
require_once 'userSystem.php';

if(empty($_POST)) {
	json_response(null,500);
}
function json_response($data=null, $httpStatus=200)
{
	header_remove();
	header("Content-Type: application/json");
	http_response_code($httpStatus);
	echo json_encode(['response' => $data]);
	exit();
}

// Database connection
try {
	$pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
	json_response('We sorry, we have currently doing maintanance jobs on our side. Please try again later.',500);
	error_log('raytrace-id ' . $e->getMessage());
	die();
}

$userSystem = new UserSystem($pdo);

// Registration form submission
if ($_POST['action'] === 'register') {
	if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		die('CSRF token validation failed');
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	$file = $_FILES['file'];

	try {
		$userSystem->register($username, $password, $file);
		json_response('Registration successful!');
	} catch (RuntimeException $e) {
		json_response('Registration failed! Please try again.', 500);
		error_log('raytrace-id ' . $e->getMessage());
	}
}

// Login form submission
if ($_POST['action'] === 'login') {
	if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		die('CSRF token validation failed');
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($userSystem->login($username, $password)) {
		json_response();
	} else {
		json_response('Invalid credentials!',500);
	}
}

if ($_POST['action'] === 'logout') {
	$_SESSION = array();
	session_destroy();
	json_response();
	exit;
}

json_response('',500);