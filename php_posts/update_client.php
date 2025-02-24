<?php
header('Content-Type: application/json; charset=utf-8');
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


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['email'], $data['name'], $data['password'])) {
    $id = $data['id'];
    $likes = $data['name'];
    $caption = $data['email'];
    $dislikes = $data['password'];

    $dislikes = password_hash($dislikes, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare('UPDATE user_account SET  name = ?,email = ?, password = ? WHERE id = ?');
        $stmt->execute([$likes,$caption,$dislikes, $id]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
