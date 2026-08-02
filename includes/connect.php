<?php
// Load environment variables
$env_file = __DIR__ . '/../.env';
if(file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line) {
        if(strpos($line, '=') !== false && $line[0] !== ';') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$con = mysqli_connect(
    $_ENV['DB_HOST'] ?? 'localhost',
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    $_ENV['DB_NAME'] ?? 'mystore',
    $_ENV['DB_PORT'] ?? 3306
);

if(!$con){
    die(mysqli_error($con));
}
?>