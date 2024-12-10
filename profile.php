<?php
session_start();
require('db.php');
include 'header.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data from database
$user_id = $_SESSION['user_id'];
$query = $db->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

// Redirect to login page if user data not found
if (!$user) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 20px;
        }

        .profile-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        .profile-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .profile-card img:hover {
            transform: scale(1.1);
        }

        .profile-card h1 {
            font-size: 2rem;
            color: #2575fc;
            margin-bottom: 15px;
        }

        .profile-card p {
            font-size: 1rem;
            color: #555;
            margin: 10px 0;
        }

        .profile-card strong {
            color: #2575fc;
        }

        .profile-card .button {
            display: inline-block;
            background: #2575fc;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        .profile-card .button:hover {
            background: #0056b3;
        }

        footer {
            text-align: center;
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card">
        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default.png'); ?>" alt="Profile Picture">
        <h1>User Profile</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="edit_profile.php" class="button">Edit Profile</a>
    </div>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
