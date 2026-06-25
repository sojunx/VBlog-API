<?php

use App\Services\CloudinaryService;
use Psr\Container\ContainerInterface;

return [
    PDO::class => require __DIR__ . '/database.php',
    CloudinaryService::class => function (ContainerInterface $c) {
        $cloudName = trim($_ENV['CLOUDINARY_CLOUD_NAME'] ?? '', "'\"");
        $apiKey = trim($_ENV['CLOUDINARY_API_KEY'] ?? '', "'\"");
        $apiSecret = trim($_ENV['CLOUDINARY_API_SECRET'] ?? '', "'\"");
        return new CloudinaryService($cloudName, $apiKey, $apiSecret);
    },
];