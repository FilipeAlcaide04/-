document.addEventListener('DOMContentLoaded', () => {
    const feedContainer = document.getElementById('feed');


    fetch('php_posts/fetch_post_trends.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(posts => {
            if (!posts || posts.length === 0) {
                feedContainer.innerHTML = '<p class="text-center text-muted">No trending posts available.</p>';
                return;
            }

            posts.forEach(post => {
                const postHTML = `
                 <div class="post-container mt-4">
                    <div class="post">
                        <div class="post-header">
                            <img src="images/profile_picture.png" alt="Profile Picture" class="profile-picture">
                            <span class="username">${post.username}</span>
                        </div>
                        <div class="post-image">
                            <img src="${post.image_path}" alt="Post Image">
                        </div>
                        <div class="post-footer">
                            <div class="actions">
                                <span class="icon" id="likeButton${post.id}" 
                                      onclick="toggleLike(${post.id}, 'likeCount${post.id}', 'dislikeButton${post.id}')">❤️</span>
                                <span class="counter" id="likeCount${post.id}">${post.likes || 0}</span>
                                <span class="icon" id="dislikeButton${post.id}" 
                                      onclick="toggleDislike(${post.id}, 'dislikeCount${post.id}', 'likeButton${post.id}')">💔</span>
                                <span class="counter" id="dislikeCount${post.id}">${post.dislikes || 0}</span>
                            </div>
                            <p><span class="username">${post.username}:</span> ${post.caption}</p>
                            <small class="timestamp">${new Date(post.created_at).toLocaleString()}</small>
                        </div>
                    </div>
                </div>`;
            
                feedContainer.innerHTML += postHTML;
            });
        })
        .catch(error => {
            console.error('Error fetching posts:', error);
            feedContainer.innerHTML = '<p class="text-center text-danger">Failed to load trending posts. Please try again later.</p>';
        });
});
