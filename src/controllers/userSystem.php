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

	public function register($username, $password, $file): bool
	{
		$username = htmlspecialchars(strip_tags($username));
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");

		$errors = $this->userDataValidation($username, $password);
		if($errors) {
			json_response($errors,500);
		}

		try {
			$result = $stmt->execute([
				':username' => $username,
				':password' => $hashedPassword
			]);

			if($result) {
				$photo = $this->handleUserPhoto($file, $this->pdo->lastInsertId());
				if($photo) {
					return true;
				}
			}
		} catch (\Throwable $e) {
			// error handling here
		}
		return false;
	}

	private function handleUserPhoto($file, $lastInsertedId): bool
	{
		$uploadDir = "uploads/";
		$uploaded = $this->handleFileUpload($file, $uploadDir);
		$stmt = $this->pdo->prepare("INSERT INTO files (file_path, file_name, user_id) VALUES (:file_path, :file_name, :user_id )");

		return $stmt->execute([
			':file_path' => $uploaded['file_path'],
			':file_name' => $uploaded['file_name'],
			':user_id' => $lastInsertedId
		]);
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
		$username = trim(htmlspecialchars(strip_tags($username)));
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
				$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
				return true;
			}
		} catch (\Throwable $e) {
			// error handling
		}

		return false;
	}

	private function getUser(): array
	{
		$stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = :username LIMIT 1");
		$stmt->execute([
			':username' => $_SESSION['user_id']
		]);

		try {
			$user = $stmt->fetch();
		} catch (\Throwable $e) {
			// error handling, example:
			echo 'We sorry, we have currently doing maintanance jobs on our side. Please try again later.';
			error_log('raytrace-id ' . $e->getMessage());
			// redirect header("Location: http://example.com/tryagain.php");
			die();
		}

		return $user;
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
		$stmt->execute([
			':user_id' => $_SESSION['user_id']
		]);

		try {
			$file = $stmt->fetch();
			if ($file) {
				return $file['file_path'] . '/' . $file['file_name'];
			}
		} catch (\Throwable $e) {
			// error handling
		}

		return '';
	}
}