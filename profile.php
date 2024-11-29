<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user data from the database
$userId = $_SESSION['user_id'];
// ... (SQL query to fetch user data)

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    </head>
<body>
    <h1>User Profile</h1>
    <img src="<?php echo $userData['profile_picture']; ?>" alt="Profile Picture">
    <p>Username: <?php echo $userData['username']; ?></p>
    <p>Email: <?php echo $userData['email']; ?></p>
    <a href="edit_profile.php">Edit Profile</a>
</body>
</html>