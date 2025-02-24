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

// Fetch logins
$sql = "SELECT id, name_user, email_user, ip_address, action_time, action_type FROM login_logs ORDER BY id DESC";
$stmt = $pdo->query($sql);
$logins = $stmt->fetchAll();

echo json_encode($logins);
?>
