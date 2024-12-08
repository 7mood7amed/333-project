<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_profile_picture = $is_logged_in && isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) 
    ? $_SESSION['profile_picture'] 
    : 'default.png'; // Fallback to 'default.png'
?>

<header>
    <div class="header-container">
        <div class="logo">
            <img src="assets/logo.png" alt="Logo" class="logo-img"> <!-- Dynamic logo path -->
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="browse_rooms.php">Browse Rooms</a>
            <a href="book_room.php">Book a Room</a>
            <?php if ($is_logged_in): ?>
                <a href="profile.php">My Profile</a> <!-- Link to user profile page -->
            <?php endif; ?>
            <a href="my_bookings.php" class="button">My Bookings</a>
        </nav>
        <div class="user-options">
            <?php if ($is_logged_in): ?>
                <!-- Add a "View My Bookings" button -->
                <a href="profile.php">
                    <img src="uploads/<?php echo htmlspecialchars($user_profile_picture); ?>" alt="Profile Picture" class="profile-pic">
                </a>
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

    .logo-img {
        height: 50px;
    }

    nav {
        flex-grow: 1;
        text-align: center;
    }

    nav a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        display: inline-block;
    }

    nav a:hover {
        background-color: #2980b9;
    }

    .user-options {
        display: flex;
        align-items: center;
        justify-content: flex-start; /* Align items to the left */
    }

    .user-options a.button {
        margin-left: 10px; /* Add space between buttons */
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

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            align-items: flex-start;
        }

        nav {
            text-align: left;
            margin-top: 10px;
        }

        .user-options {
            margin-top: 10px;
        }

        .profile-pic {
            width: 35px;
            height: 35px;
        }

        .button {
            margin-top: 5px;
        }
    }
</style>
