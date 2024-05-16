<?php
function validate_csrf_token($token = false) {
	if(!$token) {
		$token = $_POST['csrf_token'] ?? '';
	}
	if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
		json_response('Your token has expired. Please refresh the page and try again.', 403);
	}
}