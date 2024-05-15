<?php
require_once 'config.php';
try {
	$pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
	$msg = 'We sorry, we have currently doing maintanance jobs on our side. Please try again later.';
	if(!empty($_POST)) {
		require_once 'includes/json_helper.php';
		json_response($msg,500);
	} else {
		echo $msg;
	}
	error_log('raytrace-id ' . $e->getMessage());
	die();
}
