<?php
require_once __DIR__ . '/../controllers/userSystem.php';
require_once __DIR__ . '/../inc/db_connection.php';
$userSystem = new UserSystem($pdo);
$userData = $userSystem->getUserProfile();
?>
<h1>Login successful! </h1>
<form id=logoutForm method="post">
    <input type="hidden" name="action" value="logout">
    <button type="submit" name="logout">Logout</button>
</form>
<div class="user-profile">
    <div class="user-welcome">
        Hi <?= $userData['user']['username']; ?>
    </div>
    <div class="user-photo">';
		<?php if ($userData['photo']) { ?>
            <img src="<?= $userData['photo']; ?>">
		<?php } else { ?>
            no photo
		<?php } ?>
    </div>
</div>