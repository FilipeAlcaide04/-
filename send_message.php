<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $user = $_SESSION['username'];

        $conn = new mysqli("localhost", "root", "", "Buzzly_TII");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO messages (user, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $user, $message);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}
?>
