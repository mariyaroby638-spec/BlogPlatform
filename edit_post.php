
<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid post.");
}

$stmt = $conn->prepare(
    "SELECT title, content FROM posts WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    exit("Post not found or you don't have permission to edit it.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title !== "" && $content !== "") {
        $stmt = $conn->prepare(
            "UPDATE posts SET title = ?, content = ?
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param(
            "ssii", $title, $content, $id, $_SESSION['user_id']
        );
        $stmt->execute();
        $stmt->close();

        header("Location: post.php?id=" . $id);
        exit;
    } else {
        $message = "Please fill in both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog | BlogSphere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BlogSphere</h1>
    <nav><a href="index.php">Home</a></nav>
</header>

<div class="container">
    <div class="card">
        <h2>Edit Your Blog</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <form method="POST">
            <label>Title</label>
            <input type="text" name="title" maxlength="200"
                value="<?= htmlspecialchars($post['title']) ?>" required>

            <label>Content</label>
            <textarea name="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
</body>
</html>
