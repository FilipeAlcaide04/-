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

}
else{
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
    <title>Admin Dashboard Posts</title>
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
                <li class="nav-item active">
                    <a class="nav-link" href="admin_dashboard_clients.php">Posts 📁<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="admin_dashboard_clients.php">Clients 👤</a>
                </li>
                <li class="nav-item ml-3">
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
    <h1 class="text-center">Admin Dashboard For Posts 
</h1>

    <div id="postsTable" class="table-responsive mt-4">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Caption</th>
                    <th>Image</th>
                    <th>Likes</th>
                    <th>Dislikes</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Search posts by username" />
        </div>
    </div>
</div>
            <tbody id="postsBody"></tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editPostId">
                    <div class="mb-3">
                        <label for="editCaption" class="form-label">Caption</label>
                        <textarea id="editCaption" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editLikes" class="form-label">Likes</label>
                        <input type="number" id="editLikes" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="editDislikes" class="form-label">Dislikes</label>
                        <input type="number" id="editDislikes" class="form-control">
                    </div>
                    <button type="button" class="btn btn-success" id="saveEdit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const postsBody = document.getElementById('postsBody');
    const searchInput = document.getElementById('searchInput');
    let posts = [];

    // Fetch posts from server
    fetch('php_posts/fetch_posts.php')
        .then(response => response.json())
        .then(data => {
            posts = data; // Store the fetched posts
            renderPosts(posts); // Initial render
        })
        .catch(error => console.error('Error fetching posts:', error));

    // Filter posts by username
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredPosts = posts.filter(post => post.username.toLowerCase().includes(searchTerm));
        renderPosts(filteredPosts);
    });

    // Function to render posts in the table
    function renderPosts(filteredPosts) {
        postsBody.innerHTML = filteredPosts.map(post => `
            <tr>
                <td>${post.id}</td>
                <td>${post.username}</td>
                <td>${post.caption}</td>
                <td><img src="${post.image_path}" alt="Post Image" width="50"></td>
                <td>${post.likes}</td>
                <td>${post.dislikes}</td>
                <td>${new Date(post.created_at).toLocaleString()}</td>
                <td>
                    <button class="mt-2 btn btn-warning btn-sm" onclick="editPost(${post.id}, '${post.caption}', ${post.likes}, ${post.dislikes})">Edit</button>
                    <button class="mt-2 btn btn-danger btn-sm" onclick="deletePost(${post.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    // Edit post logic
    function editPost(id, caption, likes, dislikes) {
        document.getElementById('editPostId').value = id;
        document.getElementById('editCaption').value = caption;
        document.getElementById('editLikes').value = likes;
        document.getElementById('editDislikes').value = dislikes;
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    }

    document.getElementById('saveEdit').addEventListener('click', () => {
        const id = document.getElementById('editPostId').value;
        const caption = document.getElementById('editCaption').value;
        const likes = document.getElementById('editLikes').value;
        const dislikes = document.getElementById('editDislikes').value;

        fetch('php_posts/update_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, caption, likes, dislikes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Failed to update post: ' + data.message);
            }
        })
        .catch(error => console.error('Error updating post:', error));
    });

    // Delete post logic
    function deletePost(id) {
        if (!confirm('Are you sure you want to delete this post?')) return;

        fetch('php_posts/delete_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Failed to delete post: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting post:', error));
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="bootstrap-4.5.3-dist/js/bootstrap.js"></script>
<script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>

</body>
</html>
