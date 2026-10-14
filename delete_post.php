
<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid post.");
}

$stmt = $conn->prepare(
    "DELETE FROM posts WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $message = "Blog post deleted successfully.";
} else {
    $message = "Post not found or you don't have permission to delete it.";
}

$stmt->close();

header("Location: index.php");
exit;
?>
