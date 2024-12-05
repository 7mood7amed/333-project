<?php
session_start();
include 'db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_profile_picture = $is_logged_in ? $_SESSION['profile_picture'] : '';
?>

<header>
    <div class="header-container">
        <div class="logo">
            <img src="path/to/your/logo.png" alt="Logo" style="height: 50px;"> <!-- Placeholder for logo -->
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="browse_rooms.php">Browse Rooms</a>
            <a href="book_room.php">Book a Room</a>
        </nav>
        <div class="user-options">
            <?php if ($is_logged_in): ?>
                <img src="uploads/<?php echo htmlspecialchars($user_profile_picture ?: 'default.png'); ?>" alt="Profile Picture" class="profile-pic">
            <?php else: ?>
                <a href="login.php" class="button">Login</a>
                <a href="register.php" class="button">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<style>
    /* Header Styles */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #3498db;
        padding: 10px 20px;
        color: white;
    }

    .logo img {
        height: 50px; /* Adjust logo height */
    }

    nav {
        flex-grow: 1;
        text-align: center;
    }

    nav a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
    }

    nav a:hover {
        background-color: #2980b9;
    }

    .user-options {
        display: flex;
        align-items: center;
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }

    .button {
        background-color: #2980b9;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin-left: 10px;
    }

    .button:hover {
        background-color: #1a5276;
    }
</style>