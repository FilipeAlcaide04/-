<?php
session_start();

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database credentials
$host = 'localhost';
$dbname = 'Buzzly_TII';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error = '';

function logUserAction($pdo, $name, $email, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO login_logs (name_user, email_user, action_type, ip_address) VALUES (:name, :email, :action, :ip)");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':action' => $action,
        ':ip' => $ip_address
    ]);
}

// Function to send email
function sendEmail($recipientEmail, $subject, $body) {
    $mail = new PHPMailer(true);

    if ($recipientEmail === 'admin@admin.pt') {
        return; 
    }

    try {
        // Gmail SMTP server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'filipealcaide.escola@gmail.com'; 
        $mail->Password = 'ThisISNotThePassword';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('filipealcaide.escola@gmail.com', 'Buzzly Team'); 
        $mail->addAddress($recipientEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (isset($_POST['register'])) {
        $name = htmlspecialchars($_POST['name'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } elseif (strlen($password) < 5) {
            $error = "Password must be at least 5 characters long.";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $stmt = $pdo->prepare("INSERT INTO user_account (email, name, password) VALUES (:email, :name, :password_hash)");
                $stmt->execute([
                    ':email' => $email,
                    ':name' => $name,
                    ':password_hash' => $password_hash,
                ]);

                $subject = "Welcome to Buzzly - Let's Get Started!";
                $body = "
                    <h1 style='color: rgb(255, 119, 0);; font-family: Arial, sans-serif;'>Welcome to Buzzly, $name!</h1>
                    <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                        We're thrilled to have you join our community. Buzzly is your go-to platform for amazing experiences and opportunities.
                    </p>
                    <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                        Here are a few tips to get you started:
                    </p>
                    <ul style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                        <li>🔍 Explore our features to see what Buzzly has to offer.</li>
                        <li>💬 Connect with like-minded individuals and grow your network.</li>
                        <li>🌟 Stay tuned for exclusive updates, offers, and content.</li>
                    </ul>
                    <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                        If you have any questions, feel free to <a href='mailto:support@buzzly.com' style='color: #29abe2; text-decoration: none;'>contact our support team</a>. 
                        We're here to help you every step of the way.
                    </p>
                    <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>Cheers,<br>The Buzzly Team</p>
                ";
                
                sendEmail($email, $subject, $body);

                echo "<script>alert('User account created successfully! Please login.');</script>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = strpos($e->getMessage(), 'email') !== false ? 
                        "The email address is already in use." : 
                        "The username is already taken.";
                } else {
                    error_log("Database error: " . $e->getMessage());
                    $error = "An error occurred. Please try again.";
                }
            }
        }
    } elseif (isset($_POST['login'])) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM user_account WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $subject = "Buzzly Login Alert - Your Account Was Accessed";
                    $body = "
                        <h1 style='color:rgb(255, 119, 0); font-family: Arial, sans-serif;'>Hello, $user[name]!</h1>
                        <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                            We noticed a successful login to your Buzzly account just now.
                        </p>
                        <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                            <strong>Details:</strong><br>
                            - <strong>Login Time:</strong> " . date('Y-m-d H:i:s') . "<br>
                            - <strong>IP Address:</strong> " . $_SERVER['REMOTE_ADDR'] . "
                        </p>
                        <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>
                            If this was you, no further action is needed. However, if you didn't initiate this login, please reset your password immediately and contact our support team at 
                            <a href='mailto:support@buzzly.com' style='color: #29abe2; text-decoration: none;'>support@buzzly.com</a>.
                        </p>
                        <p style='font-size: 16px; font-family: Arial, sans-serif; color: #333;'>Stay safe,<br>The Buzzly Team</p>
                    ";
                    
                    sendEmail($email, $subject, $body);

                    session_regenerate_id(true);
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user['name'];
                    
                    logUserAction($pdo, $user['name'], $email, 'login');

                    header("Location: " . ($email === 'admin@admin.pt' ? 'admin_dashboard.php' : 'index.php'));
                    exit;
                } else {
                    $error = $user ? "Invalid password. Please try again." : "No account found with that email.";
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = "An error occurred. Please try again.";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzzly - Login/Register</title>
    <link href="bootstrap-4.5.3-dist/css/bootstrap.css" rel="stylesheet">
    <link href="css/login_register.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
        .toggle-btn {
            cursor: pointer;
            color: #29abe2;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1 class="tt mt-5 d-flex align-items-center justify-content-center">Buzzly</h1>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white text-center">
                        <h3 id="form-title">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" action="" id="login-form">
                            <div class="form-group">
                                <label for="login_email">Email</label>
                                <input type="email" name="email" id="login_email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="login_password">Password</label>
                                <input type="password" name="password" id="login_password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-warning btn-block mt-4">Login</button>
                            <p class="mt-3 text-center toggle-btn" onclick="toggleForms()">Don't have an account? Register here</p>
                        </form>

                        <!-- Registration Form -->
                        <form method="POST" action="" id="register-form" class="hidden">
                            <div class="form-group">
                                <label for="register_email">Email</label>
                                <input type="email" name="email" id="register_email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="register_name">Name</label>
                                <input type="text" name="name" id="register_name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="register_password">Password</label>
                                <input type="password" name="password" id="register_password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-warning btn-block mt-4">Register</button>
                            <p class="mt-3 text-center toggle-btn" onclick="toggleForms()">Already have an account? Login here</p>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>&copy; Buzzly Designed in Lisbon 2024</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const formTitle = document.getElementById('form-title');

            if (loginForm.classList.contains('hidden')) {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                formTitle.textContent = 'Login';
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                formTitle.textContent = 'Register';
            }
        }
    </script>
</body>
</html>
