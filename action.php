<?php
session_start();
require_once 'db_connection.php';
require_once 'includes/file_handling.php';
require_once 'userSystem.php';
require_once 'includes/json_helper.php';
$userSystem = new UserSystem($pdo);

if(empty($_POST)) {
	json_response(null,500);
}

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
