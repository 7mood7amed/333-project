<?php
include 'db.php';
session_start();

$errors = []; // in case of any errors


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: rooms.php');
        exit();
    } else {
        $errors[] = "Invalid credentials"; // Add error to the array
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        main.container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;     
            align-items: center;        
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        button {
            width: 100%;               
            max-width: 300px;          
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: auto;               
            padding: 10px 20px;        
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            color: red;
            text-align: center;
        }

        /* Error messages */
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?= $error ?></p>
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