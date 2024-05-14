<?php
// Config can be done in many ways
// 1. Constant (wordpress)
// 2. $_ENV (laravel)
// 3. File parsed: ini, json, yaml and not parsed: php (symfony)
// 3. Variable (classic vanilla)

$dsn = 'mysql:host=localhost;dbname=user_registration_system';
$dbUser = 'project_user';
$dbPassword = 'user_password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];
