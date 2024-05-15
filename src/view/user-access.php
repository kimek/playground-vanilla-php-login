<?php
	$csrf_token = bin2hex(random_bytes(32));
	$_SESSION['csrf_token'] = $csrf_token;
?>
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
		<label for="register-file">Upload File: (max 2 MB)</label>
		<input type="file" name="file" accept="image/*" id="register-file" required>
		<div class="tac">
			<button type="submit" name="register">Register</button>
		</div>
		<p class="change_link">
			Already a member ?
			<a href="#tologin" class="to_register"> Go and log in </a>
		</p>
	</form>
</div>
