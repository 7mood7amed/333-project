<?php
include 'db.php';
session_start();

$errors = []; // In case of any errors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Check if fields are not empty
    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required.";
    } else {
        // Query the database for user credentials
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Validate user and password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Store username in session
            header('Location: index.php'); // Redirect to the home page
            exit();
        } else {
            $errors[] = "Invalid credentials"; // Add error to the array
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <style>
        /* Basic styling for the page */
    </style>
</head>
<body>
    <main class="container">
        <h2>Login</h2>
        <?php if (!empty($errors)): ?>
            <p class="error-message"><?= implode('<br>', $errors) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required placeholder="Enter your email">
            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password">
            <button type="submit">Login</button>
        </form>
    </main>
</body>
</html>
