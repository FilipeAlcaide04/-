<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login_register.php");
    exit;
}

// Check if the user is the admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
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
    <title>Admin Dashboard Logins</title>
    <link href="bootstrap-4.5.3-dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_nav.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <style>
        #searchInput {
            width: 100%;
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Buzzly</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php">Posts 📁</a>
            </li>
            <li class="nav-item ml-3">
                <a class="nav-link" href="admin_dashboard_clients.php">Clients 👤</a>
            </li>
            <li class="nav-item ml-3 active">
                <a class="nav-link" href="admin_dashboard_logins.php">Logins 🔍</a>
            </li>
            <li class="nav-item ml-3 ">
                    <a class="nav-link" href="admin_dashboard_chat.php">Chat 📩</a>
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

<div class="dashboard-container">
    <h1 class="text-center">Admin Dashboard For Logins</h1>

    <div id="postsTable" class="table-responsive mt-4">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Action Time</th>
                    <th>Action Type</th>
                </tr>
            </thead>
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search logins by username" />
                    </div>
                </div>
            </div>
            <tbody id="postsBody"></tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const postsBody = document.getElementById('postsBody');
    const searchInput = document.getElementById('searchInput');
    let posts = [];

    // Fetch logins from server
    fetch('php_posts/fetch_logins.php')
        .then(response => response.json())
        .then(data => {
            posts = data;
            renderPosts(posts);
        })
        .catch(error => console.error('Error fetching logins:', error));

    // Filter logins by username
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredPosts = posts.filter(post => post.name_user.toLowerCase().includes(searchTerm));
        renderPosts(filteredPosts);
    });

    // Function to render logins in the table
    function renderPosts(filteredPosts) {
        postsBody.innerHTML = filteredPosts.map(post => `
            <tr>
                <td>${post.id}</td>
                <td>${post.name_user}</td>
                <td>${post.email_user}</td>
                <td>${post.ip_address}</td>
                <td>${post.action_time}</td>
                <td>${post.action_type}</td>
            </tr>
        `).join('');
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="bootstrap-4.5.3-dist/js/bootstrap.js"></script>
<script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>

</body>
</html>
