<?php

trait fileHandler
{
	private function fileUploadErrorMessage($errorCode): string
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

	private function generateUniqueFileName($directory, $fileName): string
	{
		$fileInfo = pathinfo($fileName);
		$uniqueName = $fileInfo['filename'] . '_' . uniqid() . '.' . $fileInfo['extension'];
		return $uniqueName;
	}

	private function handleFileUpload($file, $uploadDir): array
	{
		$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
		$maxFileSize = 2 * 1024 * 1024; // 2 MB

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
}
