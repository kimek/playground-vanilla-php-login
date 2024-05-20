<?php
require_once  __DIR__ . '/../inc/file_handling.php';
require_once  __DIR__ . '/../inc/json_helper.php';
class UserSystem
{
	use fileHandler;

	private PDO $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function register($username, $password, $file): array
	{
		$username = htmlspecialchars(strip_tags($username));
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");

		if($errors = $this->userDataValidation($username, $password)) {
			return [ 'error' => $errors ];
		}

		try {
			$result = $stmt->execute([
				':username' => $username,
				':password' => $hashedPassword
			]);

			if($result) {
				$photo = $this->handleUserPhoto($file, $this->pdo->lastInsertId());

				if($photo) {
					return ['success' => 'Registration successful!'];
				}

				// Keep user registered instead of losing it - progressive failure handling
				return ['success' => 'Registration completed successfully, but there was an issue with your photo upload.'];
			}
		} catch (\Throwable $e) {
			// error handling here
		}

		return [ 'error' => ['Registration failed, please try again'] ];
	}

	private function handleUserPhoto($file, $lastInsertedId): bool
	{
		$uploadDir = "uploads/";

		try {
			$uploaded = $this->handleFileUpload($file, $uploadDir);
			$stmt = $this->pdo->prepare("INSERT INTO files (file_path, file_name, user_id) VALUES (:file_path, :file_name, :user_id )");

			return $stmt->execute([
				':file_path' => $uploaded['file_path'],
				':file_name' => $uploaded['file_name'],
				':user_id' => $lastInsertedId
			]);
		} catch (\RuntimeException $e) {

		} catch (\Exception $e) {
			// implement custom json error class to handle human info errors
		}
		return false;

	}

	private function userDataValidation($username, $password) : array {
		$errors = [];

		if (empty($username) || !preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
			$errors[] = 'Username must be alphanumeric and 3-20 characters long.';
		}

		if (empty($password) || strlen($password) < 6) {
			$errors[] = 'Password must be at least 6 characters long.';
		}

		return $errors;
	}

	public function login($username, $password): bool
	{
		$username = htmlspecialchars(strip_tags($username));
		// $password = trim($password); // if spaces are excluded in password req. as its common client issue
		// no need to add here https://www.php.net/manual/en/filter.filters.sanitize.php
		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");

		try {
			$stmt->execute([
				':username' => $username
			]);

			$user = $stmt->fetch();

			if ($user && password_verify($password, $user['password'])) {
				$_SESSION['user_id'] = $user['id'];
				// $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // TODO secure from fixation attack
				// prototype create new cookie with XSS token and use it with csrf
				return true;
			}
		} catch (\Throwable $e) {
			// Error handling
		}

		return false;
	}

	private function getUser(): array
	{
		$stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = :username LIMIT 1");

		try {
			$stmt->execute([':username' => $_SESSION['user_id']]);
			$user = $stmt->fetch();

			return $user ?: [];
		} catch (\Throwable $e) {
			return [];
			// Error handling ex. add ray trace,
		}
	}

	public function getUserProfile(): array
	{
		return [
			'user' => $this->getUser(),
			'photo' => $this->getUserPhoto()
		];
	}

	private function getUserPhoto(): string
	{
		$stmt = $this->pdo->prepare("SELECT * FROM files WHERE user_id = :user_id LIMIT 1");

		try {
			$stmt->execute([
				':user_id' => $_SESSION['user_id']
			]);
			$file = $stmt->fetch();
			if ($file) {
				return $file['file_path'] . '/' . $file['file_name'];
			}
		} catch (\Throwable $e) {
			// Error handling
		}

		return '';
	}

	public function logout(): void {
		$_SESSION = array();
		session_destroy();
	}
}
