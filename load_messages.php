<?php
$conn = new mysqli("localhost", "root", "", "Buzzly_TII");
if ($conn->connect_error) {
    die("error");
}

$result = $conn->query("SELECT * FROM messages ORDER BY timestamp ASC");

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$conn->close();
?>