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
    <title>User Registration</title>
    <head>
    <meta charset="UTF-8">
    <title>User Registration</title>
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
            display: flex;              /* Use flexbox for centering */
            flex-direction: column;     /* Stack children vertically */
            align-items: center;        /* Center items horizontally */
        }

        h1, h2 {
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
            width: 100%;               /* Maintain full width */
            max-width: 300px;          /* Limit max width for better centering */
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: auto;               /* Allow button to size based on content */
            padding: 10px 20px;        /* Add extra padding for button */
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

        /* Success message */
        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
        }

        /* Error messages */
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
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