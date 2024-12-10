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
    <title>Login Page</title>
    <style>
        /* Resetting margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            color: #333;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #3498db;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            color: white;
        }

        .logo-img {
            height: 50px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin-left: 20px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        nav a:hover {
            background-color: #2980b9;
            border-radius: 5px;
            transform: translateY(-2px);
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            animation: fadeIn 1s ease-in-out;
        }

        .login-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px 60px;
            max-width: 400px;
            width: 100%;
            margin-top: 40px;
        }

        .login-form h2 {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-form label {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #333;
        }

        .login-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .login-form input:focus {
            border-color: #3498db;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .login-form button {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .login-form button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .login-form .error-message {
            color: red;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 15px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 1rem;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="assets/logo.png" alt="Logo" class="logo-img" onclick="window.location.href='index.php';">
            </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="browse_rooms.php">Browse Rooms</a>
                <a href="book_room.php">Book a Room</a>
                <a href="my_bookings.php" class="button">My Bookings</a>
            </nav>
        </div>
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

                <div class="button-container">
                    <button type="submit">Login</button>
                </div>
            </form>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Head to register</a></p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Room Booking System. All rights reserved.</p>
    </footer>
</body>
</html>
