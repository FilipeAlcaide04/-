<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database credentials
$host = 'localhost';
$dbname = 'Buzzly_TII'; // Replace with your database name
$user = 'root';         // Replace with your database username
$pass = '';             // Replace with your database password (leave blank for default)

// Test database connection
function testDatabaseConnection($host, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection successful!<br>";
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage() . "<br>";
    }
}

// Test email sending
function testEmail($recipientEmail) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'filipealcaide.escola@gmail.com'; // Replace with your Gmail
        $mail->Password = 'kpqz mkxb ijql nzjh';           // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('filipealcaide.escola@gmail.com', 'Buzzly Test'); // Replace with your email and name
        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = "Test Email from Buzzly";
        $mail->Body    = "<h1>Testing Email</h1><p>This is a test email sent via PHPMailer.</p>";

        $mail->send();
        echo "Test email sent successfully to $recipientEmail!<br>";
    } catch (Exception $e) {
        echo "Test email failed to send: " . $mail->ErrorInfo . "<br>";
    }
}

// Run tests
echo "<h1>Testing Script</h1>";

// 1. Test Database Connection
echo "<h2>Database Test</h2>";
testDatabaseConnection($host, $dbname, $user, $pass);

// 2. Test Email Sending
echo "<h2>Email Test</h2>";
testEmail('filipe.alcaide@my.istec.pt'); // Replace with your test email

?>
