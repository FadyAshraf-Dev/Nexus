<?php

declare(strict_types=1);

final class ImageService
{
    private ImageRepository $repository;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->repository = new ImageRepository($pdo);
    }
    public function uploadProductImages(
        int $productId,
        array $files
    ): array {

        $files = $this->normalizeFiles($files);

        $imagePaths = [];

        try {

            foreach ($files as $sortOrder => $file) {

                $this->validateFile($file);

                $filename = $this->generateFilename(
                    $file['name']
                );

                $imagePath = $this->moveUploadedFile(
                    $file,
                    $filename
                );

                $this->repository->create(
                    $productId,
                    $imagePath,
                    $sortOrder
                );

                $imagePaths[] = $imagePath;
            }

            return $imagePaths;

        } catch (Throwable $e) {

            $directory = $this->getUploadDirectory();

            foreach ($imagePaths as $imagePath) {

                $fullPath = $directory . basename($imagePath);

                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }

            throw $e;
        }
    }
    public function deleteImage(int $imageId): bool
    {

        $image = $this->repository->findById($imageId);
        if ($image === null) {
            // throw new RuntimeException('Image not found.');
            die('Image not found.');

        }
        $directory = $this->getUploadDirectory();
        $fullPath = $directory . DIRECTORY_SEPARATOR . basename($image["image_path"]);
        if (!is_file($fullPath)) {
            // throw new RuntimeException(
            //     'Image file is missing from storage.'
            // );
            die('Image file is missing from storage.'
            );

        }
        if (!unlink($fullPath)) {
            // throw new RuntimeException('Unable to delete image file.');
            die('Unable to delete image file.');
        }
        if (!$this->repository->delete($imageId)) {
            // throw new RuntimeException('Unable to delete image record.');
            die('Unable to delete image record.');
        }

        return true;
    }
    public function deleteProductImages(int $productId): bool
    {
        $images = $this->repository->findByProduct($productId);
        if (empty($images)) {
            return true;
        }
        $directory = $this->getUploadDirectory();

        foreach ($images as $image) {

            $fullPath = $directory . DIRECTORY_SEPARATOR . basename($image["image_path"]);

            if (!is_file($fullPath)) {
                // throw new RuntimeException(
                //     'Image file is missing from storage.'
                // );
                die('Image file is missing from storage.');
            }
            if (!unlink($fullPath)) {
                // throw new RuntimeException('Unable to delete image file.');
                die('Unable to delete image file.');

            }
        }

        if (!$this->repository->deleteByProduct($productId)) {
            // throw new RuntimeException('Unable to delete product image records.');
            die('Unable to delete product image records.');

        }

        return true;
    }
    private function validateFile(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            die('Image upload failed.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            die('Invalid uploaded file.');
        }

        $maxSize = Config::app("max_image_size");

        if ($file['size'] > $maxSize) {
            die('Image exceeds maximum size.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file($file['tmp_name']);
        if ($mime === false) {
            die('Unable to determine image type.');
        }

        $allowedTypes = Config::app("allowed_image_types");

        if (!in_array($mime, $allowedTypes, true)) {
            die('Unsupported image type.');
        }

        if (getimagesize($file['tmp_name']) === false) {
            die('File is not a valid image.');
        }


    }

    private function generateFilename(string $originalName): string
    {
        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        return sprintf(
            'product_%s.%s',
            bin2hex(random_bytes(16)),
            $extension
        );
    }

    private function moveUploadedFile(
        array $file,
        string $filename
    ): string {
        $directory = $this->getUploadDirectory();

        $destination =
            rtrim($directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $filename;

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {
            die('Unable to save uploaded image.');
        }
        $relativePath = 'uploads/products/' . $filename;

        return $relativePath;
    }

    private function getUploadDirectory(): string
    {
        $directory = Config::app("paths.product_uploads");

        echo (string) Config::app('paths.product_uploads');
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                die('Unable to create upload directory.');
            }
        }

        return $directory;
    }

    private function normalizeFiles(array $files): array
    {
        $normalized = [];

        if (!is_array($files['name'])) {

            $normalized[] = [
                'name' => $files['name'],
                'type' => $files['type'],
                'tmp_name' => $files['tmp_name'],
                'error' => $files['error'],
                'size' => $files['size'],
            ];
            return $normalized;
        }

        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            $normalized[] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
        }


        return $normalized;
    }
}
