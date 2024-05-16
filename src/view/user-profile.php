<?php
require_once __DIR__ . '/../controllers/userSystem.php';
require_once __DIR__ . '/../inc/db_connection.php';
$userSystem = new UserSystem($pdo);
$userData = $userSystem->getUserProfile();
?>
<div class="dashboard">
    <div class="dashboard-sidebar">
        <div class="user-profile">
            <div class="user-photo" style="background-image: url(<?= $userData['photo']; ?>);"></div>
            <div class="user-welcome">
                Hi <?= $userData['user']['username']; ?>
                <form id=logoutForm method="post">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" name="logout">( Logout )</button>
                </form>
            </div>
        </div>
    </div>
    <div class="dashboard-main">

        <div class="sample">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc nisl diam, tristique sit amet bibendum quis, venenatis vitae elit. Ut vitae eros consectetur sem dignissim porta. Ut eu libero elit. Proin congue felis massa, consectetur vulputate lorem elementum a. Proin ut est ut ligula vulputate pulvinar. Donec lacinia feugiat neque et mattis. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>

            <p>Curabitur faucibus id orci ut dapibus. Etiam volutpat augue mi, aliquet tempor enim porttitor eu. Maecenas eu ipsum eget arcu molestie semper. Duis eu arcu sit amet lacus egestas viverra id non mauris. Cras dignissim ipsum non urna sollicitudin, non placerat ante pharetra. In lectus ipsum, laoreet a nunc ac, elementum tincidunt justo. Mauris fringilla nisl et vehicula rhoncus. Nunc finibus et purus vel maximus. Suspendisse imperdiet odio et diam fringilla imperdiet.</p>
        </div>
    </div>
</div>
