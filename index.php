
<?php
require_once 'config/db.php';
session_start();

$result = $conn->query(
    "SELECT posts.*, users.name AS author
     FROM posts
     JOIN users ON posts.user_id = users.id
     ORDER BY posts.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogSphere | Share Your Stories</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>BlogSphere</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_post.php">Write a Blog</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <h2>Stories That Connect Us</h2>
    <p>Share your ideas, express yourself, and connect with readers.</p>
</section>

<div class="container">
    <h2>Latest Blog Posts</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="card">
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars(
                    substr($post['content'], 0, 200)
                )) ?>...</p>
                <p>By <?= htmlspecialchars($post['author']) ?></p>
                <br>
                <a class="btn" href="post.php?id=<?= $post['id'] ?>">
                    Read More & Comment
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card">
            <h3>No blog posts yet!</h3>
            <p>Be the first to share your story.</p>
            <br>
            <a class="btn" href="register.php">Join and Start Writing</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>© 2026 BlogSphere | Built for the Thiranex Internship</p>
</footer>

</body>
</html>
