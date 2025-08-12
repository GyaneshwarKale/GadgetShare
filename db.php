<?php
// db.php - configure DB connection here
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1';
$port = '3307';   // change to your MySQL port (set to '3306' if default)
$db   = 'gadgetshare';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    // Friendly error message to help debugging DB connection
    echo "Database connection failed. Edit db.php with correct credentials. Error: " . $e->getMessage();
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
?>
