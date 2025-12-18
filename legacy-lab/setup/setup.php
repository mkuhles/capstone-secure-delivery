<?php
declare(strict_types=1);

// Block web access: setup should be CLI-only
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    echo "Not Found";
    exit;
}

require __DIR__ . '/../lib/db.php';
$config = require __DIR__ . '/config.php';

$varDir = dirname($config['db_file']);
if (!is_dir($varDir)) {
    mkdir($varDir, 0777, true);
}

$options = getopt('', ['reset', 'force-passwords']) ?: [];
$reset = array_key_exists('reset', $options);
$forcePasswords = array_key_exists('force-passwords', $options);

$dbFile = $config['db_file'];
$pdo = db_connect($dbFile);

function ensureMigrationsTable(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS schema_migrations (
            version INTEGER PRIMARY KEY,
            applied_at TEXT NOT NULL
        );
    ");
}

function migrationApplied(PDO $pdo, int $version): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM schema_migrations WHERE version = :v");
    $stmt->execute([':v' => $version]);
    return (bool)$stmt->fetchColumn();
}

function markMigration(PDO $pdo, int $version): void {
    $stmt = $pdo->prepare("INSERT INTO schema_migrations(version, applied_at) VALUES(:v, :t)");
    $stmt->execute([
        ':v' => $version,
        ':t' => (new DateTimeImmutable('now'))->format(DateTimeInterface::ATOM),
    ]);
}

function resetDb(PDO $pdo): void {
    $pdo->exec("DROP TABLE IF EXISTS users;");
    $pdo->exec("DROP TABLE IF EXISTS schema_migrations;");
}

try {
    $pdo->beginTransaction();

    if ($reset) {
        resetDb($pdo);
    }

    ensureMigrationsTable($pdo);

    // Run migrations in order
    $migrationFiles = glob(__DIR__ . '/migrations/*.php');
    sort($migrationFiles);

    foreach ($migrationFiles as $file) {
        $m = require $file; // ['version'=>int, 'up'=>callable]
        $version = (int)$m['version'];

        if (!migrationApplied($pdo, $version)) {
            ($m['up'])($pdo);
            markMigration($pdo, $version);
        }
    }

    // Run seeds
    $seedUsers = $config['seed_users'];
    $seedUsersFn = require __DIR__ . '/seeds/users.php';
    $seedUsersFn($pdo, $seedUsers, $forcePasswords);

    $pdo->commit();

    echo "OK: DB ready at {$dbFile}\n";
    echo "Tips:\n";
    echo "  php setup.php --reset\n";
    echo "  php setup.php --force-passwords\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "ERROR: {$e->getMessage()}\n");
    exit(1);
}
