<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Psr\Http\Message\UploadedFileInterface;
use InvalidArgumentException;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudName, string $apiKey, string $apiSecret)
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
        ]);
    }

    /**
     * Upload an uploaded file from request (UploadedFileInterface) to Cloudinary.
     *
     * @param UploadedFileInterface $uploadedFile
     * @param string $folderName
     * @return array Array containing 'url' and 'public_id'
     */
    public function uploadUploadedFile(UploadedFileInterface $uploadedFile, string $folderName): array
    {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Uploaded file has errors.');
        }

        // Get the temp file path
        $tempFilePath = $uploadedFile->getStream()->getMetadata('uri');
        if (!$tempFilePath) {
            throw new InvalidArgumentException('Could not resolve temp file path.');
        }

        // Perform the upload using Cloudinary API
        $result = $this->cloudinary->uploadApi()->upload($tempFilePath, [
            'folder' => $folderName,
        ]);

        return [
            'url' => $result['secure_url'] ?? $result['url'],
            'public_id' => $result['public_id'],
        ];
    }

    /**
     * Delete an image from Cloudinary using its public ID.
     *
     * @param string $publicId
     * @return array
     */
    public function deleteImage(string $publicId): \Cloudinary\Api\ApiResponse
    {
        return $this->cloudinary->uploadApi()->destroy($publicId);
    }
}
