<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login_register.php");
    exit;
}

// Check if the user is the admin
if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    // Redirect admin to the admin page
    header("Location: admin_dashboard.php");
    exit;
}

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
    <title>Buzzly</title>
    <!-- Bootstrap CSS -->
    <link href="bootstrap-4.5.3-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_nav.css">
    <link rel="stylesheet" href="css/post_area_style.css">
    <link rel="stylesheet" href="css/create_post.css">
</head>

<body class="font_jet size-12">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Buzzly</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item ">
                    <a class="nav-link" href="index.php">Feed ✨<span class="sr-only"></span></a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="trending.php">Trending 🔥</a>
                </li>
                <li class="nav-item ml-3 active">
                    <a class="nav-link" href="post.php">Post 💡</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="chat.php">Chat 🌎</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="about.php">About 🔎</a>
                </li>
            </ul>
            <!-- Logout Button -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="?logout=true">Logout 🚪</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
    <div class="post-container mt-3">
  
            <!-- Post Form -->
            <form id="postForm" class="p-3" method="POST" action="submit_post.php" enctype="multipart/form-data">
                <div class="post-header mb-4 d-flex align-items-center">
                    <img src="images/profile_picture.png" alt="Profile Picture" class="profile-picture me-3 rounded-circle" style="width: 50px; height: 50px;">
                    <div class="form-group">
                    <input 
    type="text" 
    name="username" 
    id="postUsername" 
    class="form-control form-control-lg mt-3" 
    placeholder="<?php echo $_SESSION['username']; ?>" 
    value="<?php echo $_SESSION['username']; ?>" 
    readonly 
    onfocus="this.blur()" 
    required
></div>
                </div>

                <div class="form-group mb-4">
                    <label for="postImage" class="form-label h5">Select an image:</label>
                    <input type="file" name="image" id="postImage" class="form-control-file" accept="image/*" required>
                    <!-- Image preview section -->
                    <div id="imagePreview" class="mt-3"></div>
                </div>

                <div class="form-group mb-4">
                    <label for="postCaption" class="form-label h5">Write a caption:</label>
                    <textarea name="caption" id="postCaption" class="form-control form-control-lg" rows="4" placeholder="Write something interesting..." required></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-lg btn-outline-warning text-uppercase">Publish 💡</button>
                    <h6 class="d-flex align-items-center justify-content-center"> ® Buzzly Designed in Lisbon 2024</h6>
                </div>
            </form>
      
    </div>

</div>

    <script src="java_script/btn_reaction.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>
    
    <!-- JavaScript for Image Preview -->
    <script>
        document.getElementById('postImage').addEventListener('change', function(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.innerHTML = ''; // Clear any existing preview
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '300px';
                    img.classList.add('img-thumbnail', 'mt-3');
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
