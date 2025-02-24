function toggleLike(postId, likeCounterId, dislikeButtonId) {
    const likeCounter = document.getElementById(likeCounterId);
    if (!likeCounter) {
        console.error(`Element with ID ${likeCounterId} not found.`);
        return;
    }

    fetch('php_posts/like_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `post_id=${postId}&action=like`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            likeCounter.textContent = parseInt(likeCounter.textContent) + 1;
        } else {
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function toggleDislike(postId, dislikeCounterId, likeButtonId) {
    const dislikeCounter = document.getElementById(dislikeCounterId);
    if (!dislikeCounter) {
        console.error(`Element with ID ${dislikeCounterId} not found.`);
        return;
    }

    fetch('php_posts/like_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `post_id=${postId}&action=dislike`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            dislikeCounter.textContent = parseInt(dislikeCounter.textContent) + 1;
        } else {
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
