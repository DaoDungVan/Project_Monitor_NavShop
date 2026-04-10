<?php
function load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

function env_value(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

load_env_file('/etc/navshop.env');
load_env_file(dirname(__DIR__) . '/.env');

$host = env_value('DB_HOST', 'localhost');
$dbname = env_value('DB_NAME', 'navshop');
$username = env_value('DB_USER', 'root');
$password = env_value('DB_PASS', '');
$debug = filter_var(env_value('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL);

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    $message = $debug ? $e->getMessage() : 'Please check database configuration.';
    die('Database connection failed: ' . htmlspecialchars($message));
}
?>
