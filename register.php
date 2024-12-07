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
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the common stylesheet -->
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
            <input type="text" id="name" name="name" placeholder="Enter Your Full Name" value="<?php echo $name; ?>" required>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Your Username" value="<?php echo $username; ?>" required>
            <label for="email">University Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your UOB email" value="<?php echo $email; ?>" required>
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

        <!-- Link to the login page -->
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
