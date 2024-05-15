<?php session_start(); //TODO possible session fixtion, use maybe session_regenerate_id(true); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test - Mateusz Kimont - Basic User Registration and Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="user-form">
	<?php
	if (isset($_SESSION['user_id'])) {
		include_once  __DIR__ . '/../src/view/user-profile.php';
	} else {
		include_once  __DIR__ . '/../src/view/user-access.php';
	}
	?>
</div>
<script src="assets/script.js"></script>
</body>
</html>

