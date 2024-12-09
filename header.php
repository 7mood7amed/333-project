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

<header style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="img/ItRoom-Logo.png" alt="Logo" class="logo-img">
            </a>
        </div>
        <nav>
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
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-x: hidden;
        padding-top: 130px; /* Ensure there's space for the sticky header */
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
        animation: fadeInLeft 1.5s ease;
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
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    nav a:hover {
        transform: translateY(-3px);
        background-color: #2980b9;
        border-radius: 5px;
    }

    .user-options {
        display: flex;
        align-items: center;
    }

    .user-options a.button {
        margin-left: 10px;
        padding: 10px 15px;
        background-color: #2980b9;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    aside {
    width: 20%;
    background: #fff;
    padding: 20px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    height: 100vh;
    position: fixed; /* Keep sidebar fixed to the left */
    top: 70px; /* Adjust for the height of the sticky header */
    }

/* Main Content Area */
    main {
        width: 80%;
        padding: 30px;
        margin-left: 20%;
        margin-top: 130px; /* Ensure main content isn't hidden under the header */
    }

    .user-options a.button:hover {
        background-color: #1a5276;
        transform: translateY(-3px);
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        transition: transform 0.3s ease;
    }

    .profile-pic:hover {
        transform: scale(1.1);
    }

    /* Red Logout Button */
    .logout-button {
        margin-left: 10px;
        padding: 10px 15px;
        background-color: ##e74c3c;;  /* Red color */
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .logout-button:hover {
        background-color: #c0392b;  /* Darker red on hover */
        transform: translateY(-3px);
    }

    /* Animations */
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

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
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
body { overflow-x: hidden; }
</style>
