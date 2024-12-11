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
        $stmt = $db->prepare("SELECT id, username, is_admin, password, profile_picture FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?: 'default.png';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }

        header {
            background: #2575fc;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        header .logo img {
            height: 50px;
            cursor: pointer;
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        header nav a:hover {
            background: #0056b3;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            padding: 40px;
        }

        .login-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-form h2 {
            margin-bottom: 20px;
            color: #2575fc;
        }

        .login-form label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .login-form input:focus {
            border-color: #2575fc;
        }

        .login-form button {
            background: #2575fc;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .login-form button:hover {
            background: #0056b3;
        }

        .login-form .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .register-link {
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .register-link a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin-top: auto;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="assets/logo.png" alt="Logo" onclick="window.location.href='index.php';">
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="browse_rooms.php">Browse Rooms</a>
        <a href="book_room.php">Book a Room</a>
        <a href="my_bookings.php">My Bookings</a>
    </nav>
</header>

<div class="container">
    <div class="login-form">
        <h2>Login</h2>

        <?php if (!empty($errors)): ?>
            <p class="error-message"><?= implode('<br>', $errors) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : ''; ?>" required placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password">

            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>
</body>
</html>
