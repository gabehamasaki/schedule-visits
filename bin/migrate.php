<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PdoConnection;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$shouldDrop = in_array('--drop', $argv, true);

try {
    echo "[Migrator] Checking database existence...\n";

    // 1. Connect to default 'postgres' database to check/create the target database
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $user = $_ENV['DB_USERNAME'] ?? 'postgres';
    $pass = $_ENV['DB_PASSWORD'] ?? 'secret';
    $targetDb = $_ENV['DB_DATABASE'] ?? 'loop_challenge';

    $dsnDefault = "pgsql:host={$host};port={$port};dbname=postgres";
    $pdoDefault = new PDO($dsnDefault, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Check if database exists
    $stmt = $pdoDefault->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
    $stmt->execute(['dbname' => $targetDb]);

    if (!$stmt->fetch()) {
        echo "[Migrator] Database '{$targetDb}' not found. Creating it...\n";
        // Cannot use prepared statements for CREATE DATABASE
        $pdoDefault->exec("CREATE DATABASE {$targetDb}");
        echo "[Migrator] Database '{$targetDb}' created successfully.\n";
    } else {
        echo "[Migrator] Database '{$targetDb}' already exists.\n";
    }

    // Close the default connection
    $pdoDefault = null;

    // 2. Connect to the actual target database using our Singleton
    echo "[Migrator] Connecting to '{$targetDb}'...\n";
    $pdo = PdoConnection::getInstance();

    // 2.5. --drop: wipe every table so migrations/seeders start from scratch
    if ($shouldDrop) {
        echo "[Migrator] --drop passed. Dropping all tables...\n";
        $pdo->exec("DROP TABLE IF EXISTS appointments, vehicles CASCADE;");
        echo "[Migrator] Tables dropped.\n";
    }

    // 3. Run Migrations
    echo "[Migrator] Running migrations...\n";
    $migrationFiles = glob(__DIR__ . '/../database/migrations/*.sql');
    sort($migrationFiles);

    foreach ($migrationFiles as $file) {
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        echo " -> Migrated: " . basename($file) . "\n";
    }

    // 4. Run Seeders
    echo "[Migrator] Running seeders...\n";
    $seederFiles = glob(__DIR__ . '/../database/seeders/*.sql');
    sort($seederFiles);

    foreach ($seederFiles as $file) {
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        echo " -> Seeded: " . basename($file) . "\n";
    }

    echo "[Migrator] Database setup completed successfully!\n";
} catch (Exception $e) {
    echo "[Migrator] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
