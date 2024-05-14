<?php
session_start();
require_once 'config.php';
require_once 'userSystem.php';

// Database connection
try {
	$pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
	echo 'We sorry, we have currently doing maintanance jobs on our side. Please try again later.';
	error_log('raytrace-id ' . $e->getMessage());
	die();
}

$userSystem = new UserSystem($pdo);
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

//TODO possible session fixtion, use maybe session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test - Mateusz Kimont - User Registration and Login</title>
    <meta charset="UTF-8">
</head>
<link rel="stylesheet" href="style.css">
<body>
<div class="user-form">
	<?php
	if (isset($_SESSION['user_id'])) {
		$userSystem->getUserProfile();
	} else { ?>
        <a class="hiddenanchor" id="toregister"></a>
        <a class="hiddenanchor" id="tologin"></a>
        <div class="user-login">
            <h1>Login</h1>
            <form id=loginForm method="post" action="login">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <label for="login-username">Username:</label>
                <input type="text" name="username" id="login-username" placeholder="Enter your username" required>
                <label for="login-password">Password:</label>
                <input type="password" name="password" id="login-password" placeholder="Enter your password" required>
                <div class="tac">
                    <button type="submit" name="login">Login</button>
                </div>
                <p class="change_link">
                    Not a member yet ?
                    <a href="#toregister" class="to_register">Join us</a>
                </p>
            </form>
        </div>
        <div class="user-registration">
            <h1>Register</h1>
            <form id=registerForm method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <label for="register-username">Username:</label>
                <input type="text" name="username" id="register-username" placeholder="Enter your username" required>
                <label for="register-password">Password:</label>
                <input type="password" name="password" id="register-password" placeholder="Enter your password"
                       required>
                <label for="register-file">Upload File:</label>
                <input type="file" name="file" id="register-file" required>
                <div class="tac">
                    <button type="submit" name="register">Register</button>
                </div>
                <p class="change_link">
                    Already a member ?
                    <a href="#tologin" class="to_register"> Go and log in </a>
                </p>
            </form>
        </div>
		<?php
	}
	?>

</div>

<script>
    if(typeof loginForm !== 'undefined') {
        loginForm.addEventListener('submit', ajaxRequest);
        registerForm.addEventListener('submit', ajaxRequest);
    }

    if(typeof logoutForm !== 'undefined') {
        logoutForm.addEventListener('submit', ajaxRequest);
    }
    function ajaxRequest(event) {
        event.preventDefault();

        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'action.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                let response = JSON.parse(xhr.responseText).response;
                if (xhr.status === 200) {
                    if(response) {
                        alert(response);
                    } else {
                        document.location = location.protocol+'//'+location.host+location.pathname;
                    }
                } else {
                    alert(response);
                }
            }
        };
        xhr.send(new FormData(event.target));
    }
</script>
</body>
</html>

