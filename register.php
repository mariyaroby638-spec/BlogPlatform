
<?php
require_once 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 6) {
        $message = "Enter valid details. Password must be at least 6 characters.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

      

try {
    $stmt->execute();
    $message = "Registration successful! You can now log in.";
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        $message = "This email is already registered. Please log in or use another email.";
    } else {
        $message = "Registration failed. Please try again.";
    }
}

$stmt->close();

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | BlogSphere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BlogSphere</h1>
    <nav><a href="index.php">Home</a></nav>
</header>

<div class="container">
    <div class="card">
        <h2>Create Your Account</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password (minimum 6 characters)</label>
            <input type="password" name="password" minlength="6" required>

            <button type="submit">Register</button>
        </form>

        <p>Already registered? <a href="login.php">Login here</a></p>
    </div>
</div>
</body>
</html>
