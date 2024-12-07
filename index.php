
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