
<?php
session_start();
include 'db.php';
include 'header.php';

// If the user is logged in, get their username and admin status
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $is_admin = $_SESSION['is_admin'];
} else {
    $username = '';
    $is_admin = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - Room Booking System</title>
    <link rel="stylesheet" href="style-index.css">
    <style>
        /* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
 
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f9f9f9;
    color: #333;
    overflow-x: hidden; /* Prevent horizontal scrolling */
}
 
header {
    background-color: #3498db;
    color: white;
    padding: 20px;
    text-align: center;
}
 
header h1 {
    margin: 0;
    font-size: 2rem;
}
 
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
 
/* Navigation bar */
nav {
    text-align: center;
    margin-bottom: 20px;
}
 
nav a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
    font-weight: bold;
}
 
nav a:hover {
    text-decoration: underline;
}
 
/* Welcome message */
.welcome-message {
    text-align: center;
    margin-bottom: 20px;
}
 
.welcome-message p {
    font-size: 1.2rem;
}
 
/* Admin button */
.admin-button {
    text-align: center;
    margin-bottom: 30px;
}
 
.admin-button .button {
    background-color: #e74c3c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1rem;
}
 
.admin-button .button:hover {
    background-color: #c0392b;
}
 
/* Features section */
.features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}
 
.feature-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 300px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
 
.feature-card h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #333;
}
 
.feature-card p {
    font-size: 1rem;
    margin-bottom: 15px;
    color: #666;
}
 
.feature-card a {
    display: inline-block;
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1rem;
}
 
.feature-card a:hover {
    background-color: #2980b9;
}
 
/* Responsive Design */
@media (max-width: 768px) {
    .features {
        flex-direction: column;
        align-items: center;
    }
 
    .feature-card {
        width: 100%;
        max-width: 90%;
    }
 
    header h1 {
        font-size: 1.8rem;
    }
 
    .welcome-message p {
        font-size: 1rem;
    }
}
 
/* Footer */
footer {
    text-align: center;
    padding: 20px;
    background-color: #333;
    color: white;
    margin-top: 30px;
}
 
 
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Room Booking System</h1>
</header>

<div class="container">
    <!-- Navigation for logged-in users and visitors -->
    <nav>
        <?php if ($username): ?>
            <span>Hello, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="profile.php">Profile</a> |
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> |
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>

    <!-- Welcome message for logged-in users -->
    <?php if ($username): ?>
        <div class="welcome-message">
            <p>Welcome back to your dashboard! You can browse rooms, book a room, or view your profile.</p>
        </div>
    <?php else: ?>
        <div class="welcome-message">
            <p>Please log in or register to start booking rooms!</p>
        </div>
    <?php endif; ?>

    <!-- Admin access button -->
    <div class="admin-button">
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php" class="button">Go to Admin Dashboard</a>
        <?php endif; ?>
    </div>

    <!-- Features Section (Room browsing, Booking) -->
    <section class="features">
        <div class="feature-card">
            <h3>Room Browsing</h3>
            <p>Browse available rooms and check their details including capacity and equipment.</p>
            <a href="browse_rooms.php">Browse Rooms</a>
        </div>

        <div class="feature-card">
            <h3>Book a Room</h3>
            <p>Book rooms for meetings, events, and more. Manage your bookings.</p>
            <?php if ($username): ?>
                <a href="book_room.php">Book a Room</a>
            <?php else: ?>
                <a href="login.php">Login to Book</a>
            <?php endif; ?>
        </div>
    </section>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>