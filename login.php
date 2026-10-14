
<?php
require_once 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, name, password FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid email or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | BlogSphere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BlogSphere</h1>
    <nav><a href="index.php">Home</a></nav>
</header>

<div class="container">
    <div class="card">
        <h2>Welcome Back!</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <p>New user? <a href="register.php">Create an account</a></p>
    </div>
</div>
</body>
</html>
