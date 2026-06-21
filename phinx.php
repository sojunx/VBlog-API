<?php

// Load environment variables
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize environment variables from .env file
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$database = $_ENV['DB_DATABASE'] ?? 'local_db';

return
    [
        'paths' => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
            'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'local' => [
                'adapter' => 'mysql',
                'host' => $host,
                'name' => $database,
                'user' => $user,
                'pass' => $password,
                'port' => '3306',
                'charset' => 'utf8',
            ],
            'production' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'production_db',
                'user' => 'root',
                'pass' => '',
                'port' => '3306',
                'charset' => 'utf8',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'development_db',
                'user' => 'root',
                'pass' => '',
                'port' => '3306',
                'charset' => 'utf8',
            ],
            'testing' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'testing_db',
                'user' => 'root',
                'pass' => '',
                'port' => '3306',
                'charset' => 'utf8',
            ]
        ],
        'version_order' => 'creation'
    ];
