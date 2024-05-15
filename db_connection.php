<?php
require_once 'config.php';
try {
	$pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
	echo 'We sorry, we have currently doing maintanance jobs on our side. Please try again later.';
	error_log('raytrace-id ' . $e->getMessage());
	die();
}
