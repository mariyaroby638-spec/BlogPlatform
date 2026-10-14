
<?php
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method not allowed'
    ]);
    exit;
}

$sql = "SELECT posts.id, posts.title, posts.content,
               users.name AS author, posts.created_at
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.id DESC";

$result = $conn->query($sql);
$posts = [];

while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode([
    'success' => true,
    'posts' => $posts
]);
?>
