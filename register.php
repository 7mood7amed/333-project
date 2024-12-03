<?php
session_start();
require('db.php'); // Including the database

// Initialize variables
$name = $username = $email = $password = "";
$errors = []; // in case of any errors
$success = false; 

// form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['pass']);

    // Validation of the email (only UOB students)
    if (!preg_match("/@stu\.uob\.edu\.bh/", $email)) {
        // store the error in errors database
        $errors[] = "Invalid email address. Only UOB emails are allowed."; 
    }

    // Checking if email exists
    $sql = "SELECT email FROM users WHERE email=?";
    $statement = $db->prepare($sql);
    $result->bindParam(1, $email);
    $result->execute();
    if ($result->rowCount() > 0) { // if true , at least oe row matches , which means email already exists 
        $errors[] = "An account with that email address already exists.";
    }

    // handling username errors (must not be less than 3 charecters and not more than 16 and cannot have spaces)
    // strlen method to count the length of characters
    if (strlen($username) < 3 || strlen($username) > 16 || preg_match('/\s/', $username)) {
        $errors[] = "Username must be 3-16 characters and cannot contain spaces.";
    }

    // Check if username exists
    $result = $db->prepare("SELECT username FROM users WHERE username=?");
    $result->bindValue(1, $username);
    $result->execute();
    if ($result->rowCount() > 0) { 
        $errors[] = "An account with that username already exists.";
    }

    // Validation of password
    if (strlen($password) < 6 || strlen($password) > 16) { // if true , at least oe row matches , which means email already exists 
        $errors[] = "Password must be between 6 and 16 characters.";
    }

    // Check if user accepted terms 
    if (!isset($_POST['terms'])) {
        $errors[] = "You must accept the terms and conditions to register.";
    }

    // If no errors, proceed register the user
    if (empty($errors)) { // empty function to check if errors database is empty
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        $statement->bindParam(':name', $name);
        $statement->bindParam(':username', $username);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password', $hashed_password); 


        if ($statementt->rowCount() == 1) { // checks the number of rows affected by the last executed SQL statement stored in the $stmt variable.
            $success = true; // true if
            $_SESSION['active_user'] = $username;
            header("Location: profile.php"); // Redirect to profile page
            exit();
        } else { // errors like duplicate entries 
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pico-css@1.5.2/dist/pico.min.css">
    <title>User Registration</title>
</head>
<body>

<main class="container">
    <h1>Registration</h1>

    <?php if ($success): ?>
        <p>Registration successful! Please <a href="login.php">login</a>.</p>
    <?php else: ?>
        <form action="" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter Your Full Name" value="<?php echo $name; ?>" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Your Username" value="<?php echo $username; ?>" required>

            <label for="email">University Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your UOB email" value="<?php echo $email; ?>" required>

            <label for="password">Password</label>
            <input type="password" name="pass" id="password" placeholder="Enter Your Password" required>

            <button type="submit">Register</button>
        </form>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">     <!-- displaye erros in red color -->
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

</body>
</html>