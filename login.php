<?php
include 'db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required.";
    } else {
        $stmt = $db->prepare("SELECT id, username, is_admin, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin']; // Store as boolean

            // Redirect to the homepage
            header('Location: index.php');
            exit();
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the common stylesheet -->
</head>
<body>
<div class="container">
    <div class="left-section">
        <h1>Welcome Back</h1>
        <p>Please log in to continue.</p>
    </div>

    <div class="right-section">
        <h2>Login</h2>
        <?php if (!empty($errors)): ?>
            <p class="error-message" style="color: red; text-align: center;"><?= implode('<br>', $errors) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required placeholder="Enter your email">
            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password">
            <div class="button-container">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>