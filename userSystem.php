<?php
class UserSystem
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function register($username, $password, $file)
	{
		// Username and password should be validated (uppercase, special chars, length, against common password etc)
		// Above not implemented due lack of specs.

		$username = htmlspecialchars(strip_tags($username));
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
		$stmt->execute([
			':username' => $username,
			':password' => $hashedPassword
		]);

		$lastInsertedId = $this->pdo->lastInsertId();
		$uploaded = $this->handleFileUpload($file);
		$stmt = $this->pdo->prepare("INSERT INTO files (file_path, file_name, user_id) VALUES (:file_path, :file_name, :user_id )");
		$stmt->execute([
			':file_path' => $uploaded['file_path'],
			':file_name' => $uploaded['file_name'],
			':user_id' => $lastInsertedId
		]);

	}

	public function login($username, $password)
	{
		$username = htmlspecialchars(strip_tags($username));

		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->execute([':username' => $username]);
		$user = $stmt->fetch();

		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
			return true;
		}
		return false;
	}

	private function getUser()
	{
		$stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = :username LIMIT 1");
		$stmt->execute([':username' => $_SESSION['user_id']]);
		try {
			$user = $stmt->fetch();
		} catch (\Exception $e) {
			echo 'We sorry, we have currently doing maintanance jobs on our side. Please try again later.';
			error_log('raytrace-id ' . $e->getMessage());
			die(); // redirect
		}

		return $user;
	}

	public function getUserProfile()
	{
		$user = $this->getUser();
		echo '
		<h1>Login successful! </h1>
		 <form id=logoutForm method="post">
		 	<input type="hidden" name="action" value="logout">
			<button type="submit" name="logout">Logout</button>
		</form>
        <div class="user-profile">
			<div class="user-welcome">
				Hi ' . $user['username'] . '
			</div>
		<div class="user-photo">';
		if ($photo = $this->getUserPhoto()) {
			echo '<img src="' . $photo . '">';
		} else {
			echo 'no photo';
		}
		echo '</div></div>';
	}

	private function handleFileUpload($file)
	{
		$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
		$maxFileSize = 2 * 1024 * 1024; // 2 MB
		$uploadDir = "uploads/";

		// Check for file upload errors
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new RuntimeException('File upload error: ' . $this->fileUploadErrorMessage($file['error']));
		}

		// Check file type
		if (!in_array($file['type'], $allowedTypes)) {
			throw new RuntimeException('Invalid file type. Allowed types are: ' . implode(', ', $allowedTypes));
		}

		// Check file size
		if ($file['size'] > $maxFileSize) {
			throw new RuntimeException('File size exceeds limit of 2 MB.');
		}

		// Generate a unique name for the file to avoid overwriting
		$fileName = $this->generateUniqueFileName($uploadDir, $file['name']);
		$targetFile = $uploadDir . $fileName;

		// Move the uploaded file
		if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
			throw new RuntimeException('Failed to move uploaded file.');
		}

		return ['file_path' => $uploadDir, 'file_name' => $fileName];
	}

	// Function to generate a unique file name
	private function generateUniqueFileName($directory, $fileName)
	{
		$fileInfo = pathinfo($fileName);
		$uniqueName = $fileInfo['filename'] . '_' . uniqid() . '.' . $fileInfo['extension'];
		return $uniqueName;
	}

	// Function to return a human-readable file upload error message
	private function fileUploadErrorMessage($errorCode)
	{
		switch ($errorCode) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded.';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder.';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk.';
			case UPLOAD_ERR_EXTENSION:
				return 'A PHP extension stopped the file upload.';
			default:
				return 'Unknown upload error.';
		}
	}

	private function getUserPhoto()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM files WHERE user_id = :user_id LIMIT 1");
		$stmt->execute([':user_id' => $_SESSION['user_id']]);
		$file = $stmt->fetch();
		if ($file) {
			return $file['file_path'] . '/' . $file['file_name'];
		}

		return false;
	}
}