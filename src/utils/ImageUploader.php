<?php

namespace MyApp\utils;

use MyApp\db\User;

require __DIR__ . "/../../vendor/autoload.php";

class ImageUploader
{
    private array $allowedMimeTypes;
    private int $maxSize;
    private string $uploadDir;

    public function __construct(array $allowedMimeTypes = Constants::ALLOWED_IMAGE_TYPES, int $maxSize = Constants::MAX_IMAGE_SIZE, string $uploadDir = __DIR__ . "/../../public/img/")
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSize = $maxSize;
        $this->uploadDir = $uploadDir;
    }

    public function upload(User $user, array $imageData): string|false
    {
        if ($this->validateImage($imageData) && ($newImagePath = $this->moveUploadedFile($imageData['tmp_name'], $imageData['name']))) {
            $this->updateUserImage($user, $newImagePath);
            $_SESSION['success_image'] = "Image updated successfully";
            return $newImagePath;
        }
        return false;
    }

    private function validateImage(array $imageData): bool
    {
        $_SESSION["error_image"] = match (true) {
            ($imageData['error'] !== UPLOAD_ERR_OK) => 'Error uploading image. Please try again.',
            (!in_array($imageData['type'], $this->allowedMimeTypes)) => 'Invalid image type. Allowed types: ' . implode(', ', array_map(fn($mimeType) => str_replace("image/", "", $mimeType), $this->allowedMimeTypes)),
            ($imageData['size'] > $this->maxSize) => 'Image size exceeds the maximum allowed size of ' . ($this->maxSize / (1024 * 1024)) . ' MB.',
            default => null,
        };
        if ($_SESSION["error_image"]) return false;
        unset($_SESSION["error_image"]);
        return true;
    }

    private function moveUploadedFile(string $tmpFile, string $imageName): string|false
    {
        if (!is_uploaded_file($tmpFile)) {
            $_SESSION["error_upload"] = 'Invalid file upload detected.';
            return false;
        }
        $uniqueFileName = uniqid() . '-' . basename($imageName);
        if (!move_uploaded_file($tmpFile, $this->uploadDir . $uniqueFileName)) {
            $_SESSION["error_upload"] = 'Failed to move uploaded image.';
            return false;
        }
        return $uniqueFileName;
    }

    private function updateUserImage(User $user, string $newImagePath): void
    {
        $currentImagePath = $user->getImage();
        $user->setImage("img/" . $newImagePath)->updateUser($user->getUsername());
        if ($currentImagePath && basename($currentImagePath) !== Constants::DEFAULT_IMAGE) $this->deleteImage($currentImagePath);
    }

    private function deleteImage(string $imagePath): void
    {
        $fullPath = $this->uploadDir . basename($imagePath);
        if (file_exists($fullPath)) unlink($fullPath);
    }

    public function resetToDefaultImage(User $user): void
    {
        self::updateUserImage($user, Constants::DEFAULT_IMAGE);
    }
}
