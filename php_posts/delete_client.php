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

if (isset($data['id'])) {
    $id = $data['id'];

    try {
        // Start a transaction to ensure both operations are successful or rolled back together
        $pdo->beginTransaction();

        // Step 1: Delete posts related to the user
        $stmt = $pdo->prepare('DELETE FROM post WHERE user_id = ?');
        $stmt->execute([$id]);

        // Step 2: Now, delete the user
        $stmt = $pdo->prepare('DELETE FROM user_account WHERE id = ?');
        $stmt->execute([$id]);

        // Commit the transaction if both deletions were successful
        $pdo->commit();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $pdo->rollBack();

        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}

?>
