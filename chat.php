<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login_register.php");
    exit;
}

// Check if the user is the admin
if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}faja

// Handle logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login_register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzzly Chat</title>
    <link href="bootstrap-4.5.3-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_nav.css">
    <link rel="stylesheet" href="css/post_area_style.css">
    <link rel="stylesheet" href="css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body class="font_jet size-12">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Buzzly</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Feed ✨</a></li>
                <li class="nav-item ml-3"><a class="nav-link" href="trending.php">Trending 🔥</a></li>
                <li class="nav-item ml-3"><a class="nav-link" href="post.php">Post 💡</a></li>
                <li class="nav-item ml-3 active"><a class="nav-link" href="chat.php">Chat 🌎</a></li>
                <li class="nav-item ml-3"><a class="nav-link" href="about.php">About 🔎</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="?logout=true">Logout 🚪</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
    <div class="chat-box  p-4" id="chat-box"></div>

    <!-- Área de input de mensagem -->
    <div class="input-group mt-2">
        <input type="text" id="message" class="form-control" autocomplete="off" placeholder="Digite uma mensagem...">


        <label for="file-input" class="btn chat-btn mt-2" class="upload-label">
        <i class="fas fa-paperclip"></i>
    </label>
    <input type="file" id="file-input" accept="image/*,video/*">
        <!-- Botão de Enviar -->
        <button class="btn chat-btn" id="send-btn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

</div>
</div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="java_script/btn_reaction.js"></script>
    <script src="java_script/load_post.js"></script>
    <script src="java_script/chat.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>
</body>
<script>
    var currentUser = "<?php echo $_SESSION['username']; ?>";
</script>
</html>
