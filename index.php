<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';
include 'header.php'; // This will now handle the header

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_profile_picture = $is_logged_in && isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) 
    ? $_SESSION['profile_picture'] 
    : 'default.png'; // Fallback to 'default.png'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - Room Booking System</title>
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
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
        }

        .hero-section {
            position: relative;
            text-align: center;
            padding: 100px 20px;
            background-image: url('img/bac.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            height: 500px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1; 
        }

        .hero-section h1, .hero-section p {
            position: relative;
            z-index: 2;
        }

        .hero-section p {
            font-size: 1.2rem;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 20px auto;
            padding: 0 20px;
        }

        .feature-card {
            width: 300px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .feature-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .feature-card p {
            margin-bottom: 15px;
            color: #666;
        }

        .feature-card a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2575fc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .feature-card a:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <h1>Welcome to the Room Booking System</h1>
        <p>Find and book the perfect room for your events and meetings with ease.</p>
    </div>

    <div class="container">
        <section class="features">
            <div class="feature-card">
                <img src="img/b1.jpg" alt="Room Browsing">
                <h3>Room Browsing</h3>
                <p>Explore available rooms, check capacity, and view details with ease.</p>
                <a href="browse_rooms.php">Browse Rooms</a>
            </div>
            <div class="feature-card">
                <img src="img/b2.jpg" alt="Book a Room">
                <h3>Book a Room</h3>
                <p>Reserve rooms for meetings, conferences, or personal events in just a few clicks.</p>
                <a href="book_room.php">Book Now</a>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2024 Room Booking System. All rights reserved.</p>
    </footer>
</body>
</html>
