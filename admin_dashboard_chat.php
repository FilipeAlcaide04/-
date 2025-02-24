<?php
session_start();

// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
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
    <title>Admin Dashboard Chat</title>
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
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" 
    aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">Posts 📁<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="admin_dashboard_clients.php">Clients 👤</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="admin_dashboard_logins.php">Logins 🔍</a>
                </li>
                <li class="nav-item ml-3 active">
                    <a class="nav-link" href="admin_dashboard_logins.php">Chat 📩</a>
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
    <h1 class="text-center">Admin Dashboard For Chat
</h1>

    <div class="text-center"></div>
    <div id="postsTable" class="table-responsive mt-4">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th >ID MSG</th>
                    <th>Username</th>
                    <th>Message</th>
                    <th>Type</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Search clients by username" />
        </div>
    </div>
</div>
            <tbody id="postsBody"></tbody>
        </table>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const postsBody = document.getElementById('postsBody');
    const searchInput = document.getElementById('searchInput');

    let clients = [];

    // Fetch clients
    fetch('php_posts/fetch_chat.php')
        .then(response => response.json())
        .then(data => {
            clients = data; 
            renderClients(clients); 
        })
        .catch(console.error);

    // Filter messages by name
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredClients = clients.filter(client => client.user.toLowerCase().includes(searchTerm));
        renderClients(filteredClients);
    });

    // Function to render the clients
    function renderClients(filteredClients) {
        postsBody.innerHTML = filteredClients.map(client => `
            <tr>
                <td>${client.id}</td>
                <td>${client.user}</td>
                <td>${client.message}</td>
                <td>${client.type}</td>
                <td>${client.timestamp}</td>
                <td>
                <button class="btn btn-danger btn-sm" onclick="deletePost(${client.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    

    // Delete client
    window.deletePost = id => {
        if (!confirm('Are you sure you want to delete this message?')) return;

        fetch('php_posts/delete_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Failed to delete client: ' + data.message);
            }
        })
        .catch(console.error);
    };
});
</script>

</body>
</html>
