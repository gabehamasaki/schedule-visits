<?php

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class PdoConnection
{
    private static ?PDO $connection = null;

    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

    public static function getInstance(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = getenv('DB_HOST');
                $db   = getenv('DB_NAME');
                $user = getenv('DB_USER');
                $pass = getenv('DB_PASSWORD');
                $port = getenv('DB_PORT') ?: 5432; // Default PostgreSQL port
                $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("[PdoConnection] Database connection error: " . $e->getMessage());

                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database connection failed.'
                ]);
                exit();
            }
        }

        return self::$connection;
    }
}
