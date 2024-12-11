<?php
session_start();
require('db.php');

$name = $username = $email = $password = "";
$errors = []; 
$success = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['pass']));

    if (!preg_match("/@stu\.uob\.edu\.bh/", $email)) {
        $errors[] = "Invalid email address. Only UOB emails are allowed."; 
    }

    $sql = "SELECT email FROM users WHERE email=?";
    $statement = $db->prepare($sql);
    $statement->bindParam(1, $email);
    $statement->execute();
    if ($statement->rowCount() > 0) {
        $errors[] = "An account with that email address already exists.";
    }

    if (strlen($username) < 3 || strlen($username) > 16 || preg_match('/\s/', $username)) {
        $errors[] = "Username must be 3-16 characters and cannot contain spaces.";
    }

    $result = $db->prepare("SELECT username FROM users WHERE username=?");
    $result->bindValue(1, $username);
    $result->execute();
    if ($result->rowCount() > 0) { 
        $errors[] = "An account with that username already exists.";
    }

    if (strlen($password) < 6 || strlen($password) > 16) {
        $errors[] = "Password must be between 6 and 16 characters.";
    }

    if (!isset($_POST['terms'])) {
        $errors[] = "You must accept the terms and conditions to register.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        $statement->bindParam(1, $name);
        $statement->bindParam(2, $username);
        $statement->bindParam(3, $email);
        $statement->bindParam(4, $hashed_password);

        if ($statement->execute()) {
            $success = true; 
            $_SESSION['active_user'] = $username;
            session_regenerate_id(true);
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = "Failed to register. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            display: flex;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 90%;
            max-width: 800px;
        }

        .left-section {
            background: #2575fc;
            color: white;
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-section h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .left-section p {
            font-size: 1.2rem;
            text-align: center;
        }

        .right-section {
            flex: 1;
            padding: 40px 20px;
        }

        .right-section h2 {
            font-size: 2rem;
            color: #2575fc;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        form input {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        form input:focus {
            border-color: #2575fc;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-container input {
            margin-right: 10px;
        }

        .checkbox-container a {
            color: #2575fc;
            text-decoration: none;
        }

        .checkbox-container a:hover {
            text-decoration: underline;
        }

        .button-container {
            text-align: center;
        }

        .btn-primary {
            background: #2575fc;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-messages {
            color: red;
            margin-top: 15px;
        }

        .error-messages p {
            margin: 0;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-section {
                padding: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left-section">
        <h1>Welcome to IT College</h1>
        <p>Room Booking System</p>
    </div>

    <div class="right-section">
        <h2>Create Your Account</h2>
        <form action="" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter Your Full Name" value="<?= htmlspecialchars($name) ?>" required>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Your Username" value="<?= htmlspecialchars($username) ?>" required>
            <label for="email">University Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your UOB email" value="<?= htmlspecialchars($email) ?>" required>
            <label for="password">Password</label>
            <input type="password" name="pass" id="password" placeholder="Enter Your Password" required>
            <div class="checkbox-container">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">By signing, agree with <a href="#">Terms & Policy</a></label>
            </div>
            <div class="button-container">
                <button type="submit" class="btn-primary">Sign up</button>
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
