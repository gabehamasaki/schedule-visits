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
            $host = str_replace(['"', "'", ';'], '', trim($_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? 'db'));
            $port = str_replace(['"', "'", ';'], '', trim($_SERVER['DB_PORT'] ?? $_ENV['DB_PORT'] ?? '5432'));
            $db   = str_replace(['"', "'", ';'], '', trim($_SERVER['DB_DATABASE'] ?? $_ENV['DB_DATABASE'] ?? 'schedule_visits'));
            $user = str_replace(['"', "'", ';'], '', trim($_SERVER['DB_USERNAME'] ?? $_ENV['DB_USERNAME'] ?? 'postgres'));
            $pass = str_replace(['"', "'", ';'], '', trim($_SERVER['DB_PASSWORD'] ?? $_ENV['DB_PASSWORD'] ?? 'secret'));

            if (empty($db)) {
                $db = 'schedule_visits';
            }

            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("[PdoConnection] Connection failed: " . $e->getMessage());
                throw $e;
            }
        }

        return self::$connection;
    }
}
