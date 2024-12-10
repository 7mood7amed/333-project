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
            <a href="index.php">
                <img src="img/ItRoom-Logo.png" alt="Logo" class="logo-img">
            </a>
        </div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="browse_rooms.php">Browse Rooms</a>
            <a href="book_room.php">Book a Room</a>
            <?php if ($is_logged_in): ?>
                <a href="profile.php">My Profile</a>
            <?php endif; ?>
            <a href="my_bookings.php" class="button">My Bookings</a>
        </nav>
        <div class="user-options">
            <?php if ($is_logged_in): ?>
                <a href="profile.php">
                    <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" class="profile-pic">
                </a>
                <a href="logout.php" class="button logout-button">Logout</a>
            <?php else: ?>
                <a href="login.php" class="button">Login</a>
                <a href="register.php" class="button">Register</a>
            <?php endif; ?>
        </div>
        <div class="burger-menu">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>
</header>

<style>
    /* General Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #e0f7fa;
        min-height: 100vh;
        overflow-x: hidden; /* Prevent horizontal scrolling */
        padding-top: 80px; /* Ensure content starts below the fixed header */
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background-color: #3498db;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
    }

    .nav-links {
        flex-grow: 1;
        text-align: center;
    }

    .nav-links a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        display: inline-block;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .nav-links a:hover {
        transform: translateY(-3px);
        background-color: #2980b9;
        border-radius: 5px;
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
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .profile-pic:hover {
        transform: scale(1.1);
    }

    .logout-button {
        margin-left: 10px;
        padding: 10px 15px;
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .logout-button:hover {
        background-color: #c0392b;
        transform: translateY(-3px);
    }

    /* Burger Menu */
    .burger-menu {
        display: none;
        flex-direction: column;
        cursor: pointer;
    }

    .burger-menu .line {
        width: 25px;
        height: 3px;
        background-color: white;
        margin: 3px 0;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .nav-links {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 80px;
            left: 0;
            background-color: #3498db;
            width: 100%;
            text-align: center;
            z-index: 999;
        }

        .nav-links.active {
            display: flex;
        }

        .burger-menu {
            display: flex;
        }

        .user-options {
            flex-direction: column;
            margin-top: 10px;
        }

        .nav-links a {
            padding: 15px;
        }
    }
</style>

<script src="header-script.js"></script>
