<?php
$host = '127.0.0.1';
$db = 'Buzzly_TII';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

// Adjusted query to join post with user_account to retrieve username
$sql = "SELECT 
        id,
        user,
        message,
        timestamp,
        type from messages";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();

echo json_encode($posts);
?>
