
<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title !== "" && $content !== "") {
        $stmt = $conn->prepare(
            "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)"
        );
        $stmt->bind_param(
            "iss",
            $_SESSION['user_id'],
            $title,
            $content
        );

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $message = "Could not publish your post.";
        }
        $stmt->close();
    } else {
        $message = "Please enter both a title and content.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Blog | BlogSphere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BlogSphere</h1>
    <nav><a href="index.php">Home</a></nav>
</header>

<div class="container">
    <div class="card">
        <h2>Write Your Blog</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <form method="POST">
            <label>Blog Title</label>
            <input type="text" name="title" maxlength="200" required>

            <label>Blog Content</label>
            <textarea name="content" rows="10" required></textarea>

            <button type="submit">Publish Blog</button>
        </form>
    </div>
</div>
</body>
</html>
