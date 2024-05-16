<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test - Mateusz Kimont - Basic User Registration and Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css?version=1.1">
</head>
<body>
<?php
if (isset($_SESSION['user_id'])) {
	include_once __DIR__ . '/user-profile.php';
} else {
	include_once __DIR__ . '/user-access.php';
}
?>
<script src="assets/script.js?version=1.1"></script>
</body>
</html>

