
<?php
require_once 'config/db.php';
session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid blog post.");
}

$stmt = $conn->prepare(
    "SELECT posts.*, users.name AS author
     FROM posts
     JOIN users ON posts.user_id = users.id
     WHERE posts.id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    exit("Blog post not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $comment = trim($_POST['comment'] ?? '');

    if ($comment !== "") {
        $stmt = $conn->prepare(
            "INSERT INTO comments (post_id, user_id, comment)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iis", $id, $_SESSION['user_id'], $comment);
        $stmt->execute();
        $stmt->close();

        header("Location: post.php?id=" . $id);
        exit;
    } else {
        $message = "Please enter a comment.";
    }
}

$stmt = $conn->prepare(
    "SELECT comments.*, users.name AS author
     FROM comments
     JOIN users ON comments.user_id = users.id
     WHERE comments.post_id = ?
     ORDER BY comments.id DESC"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$comments = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> | BlogSphere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BlogSphere</h1>
    <nav><a href="index.php">Home</a></nav>
</header>

<div class="container">
    <div class="card">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p>Written by <?= htmlspecialchars($post['author']) ?></p>
        <br>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    </div>

    <div class="card">
        <h2>Comments</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <textarea name="comment" rows="3"
                    placeholder="Write your comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Log in</a> to leave a comment.</p>
        <?php endif; ?>

        <?php while ($comment = $comments->fetch_assoc()): ?>
            <div class="card">
                <strong><?= htmlspecialchars($comment['author']) ?></strong>
                <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
           
<?php if (
    isset($_SESSION['user_id']) &&
    $_SESSION['user_id'] == $post['user_id']
): ?>
    <p>
        <a class="btn" href="edit_post.php?id=<?= $post['id'] ?>">
            Edit Post
        </a>

        <form action="delete_post.php" method="POST"
              onsubmit="return confirm('Delete this blog post?');">
            <input type="hidden" name="id"
                   value="<?= $post['id'] ?>">
            <button type="submit">Delete Post</button>
        </form>
    </p>
<?php endif; ?>
```

This displays the controls only to the author of the post. The backend also checks ownership before allowing changes.

## Step 20: Test everything

- [ ] Register and log in.
- [ ] Create a blog post.
- [ ] Open the post and add a comment.
- [ ] Edit the post and save changes.
- [ ] Delete the post and confirm it disappears.

Once these work, we'll prepare your GitHub submission.

**One important note:** your current project uses ordinary PHP pages rather than a separate REST API. Since Thiranex explicitly mentions RESTful APIs, we'll need to add a small API endpoint before calling the project fully complete. We can keep it simple.


            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
?>
