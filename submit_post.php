<?php
$host = '127.0.0.1';
$db = 'Buzzly_TII';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'] ?? '';
    $caption = $_POST['caption'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (empty($username) || empty($caption) || !$image) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Get user_id from the user_account table
    $stmt = $pdo->prepare("SELECT id FROM user_account WHERE name = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user.']);
        exit;
    }

    $user_id = $user['id'];

    // Process image upload
    $uploadDir = 'uploads/';

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create the upload directory.']);
            exit;
        }
    }

    // Ensure the upload directory is writable
    if (!is_writable($uploadDir)) {
        echo json_encode(['status' => 'error', 'message' => 'The upload directory is not writable.']);
        exit;
    }

    // Generate a unique name for the file
    $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('img_', true) . '.' . $fileExtension; // e.g., img_64b1e77a5c8a7.123456.jpg
    $imagePath = $uploadDir . $uniqueName;

    // Move the uploaded file
    if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload the image.']);
        exit;
    }

    // Insert post into the database
    $sql = "INSERT INTO post (user_id, caption, image_path) VALUES (:user_id, :caption, :image_path)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':caption' => $caption,
        ':image_path' => $imagePath,
    ]);

    echo "<script>
    alert('Thanks for posting, Enjoy the ride :)');
    window.location.href='index.php'; 
  </script>";
}
?>
