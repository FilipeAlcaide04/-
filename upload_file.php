<?php
session_start();
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado.");
}

if (!isset($_FILES['file'])) {
    die("Erro: Nenhum arquivo foi enviado.");
}

$uploadDir = "uploads_chat/";
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];

$file = $_FILES['file'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    die("Erro: Tipo de arquivo não permitido.");
}

// Criar a pasta se não existir
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = time() . "_" . basename($file['name']);
$filePath = $uploadDir . $fileName;

if (move_uploaded_file($file['tmp_name'], $filePath)) {
    $fileTypeCategory = strpos($fileType, "image") !== false ? "image" : "video";
    $username = $_SESSION['username'];

    $conn = new mysqli("localhost", "root", "", "Buzzly_TII");
    if ($conn->connect_error) {
        die("Erro de conexão com o banco de dados: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO messages (user, message, type) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Erro no SQL: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $fileName, $fileTypeCategory);
    if ($stmt->execute()) {
        echo $fileName;
    } else {
        die("Erro ao salvar no banco de dados: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Erro ao mover o arquivo para a pasta de uploads.");
}
?>
