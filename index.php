<?php
session_start();
include 'db.php';

// If the user is logged in, get their username and admin status
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $is_admin = $_SESSION['is_admin'];
} else {
    $username = '';
    $is_admin = false;
}

// Debugging output (remove in production)
echo "<pre>";
echo "User ID: " . htmlspecialchars($user_id) . "<br>";
echo "Username: " . htmlspecialchars($username) . "<br>";
echo "Is Admin: " . ($is_admin ? 'Yes' : 'No') . "<br>";
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - Room Booking System</title>
    <style>
        /* General styles for the home page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3498db;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        nav {
            margin: 20px 0;
            text-align: center;
        }

        nav a {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            margin: 0 10px;
            border-radius: 5px;
            font-size: 16px;
        }

        nav a:hover {
            background-color: #2980b9;
        }

        .welcome-message {
            text-align: center;
            margin-top: 30px;
            font-size: 18px;
        }

        .features {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }

        .feature-card {
            background-color: white;
            padding: 20px;
            width: 30%;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .feature-card h3 {
            margin-bottom: 15px;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 50px;
        }

        .admin-button {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }

        .button:hover {
            background-color: #2980b9;
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
    <div class="features">
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
    </div>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>