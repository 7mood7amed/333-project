<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Admin Panel Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .menu {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .menu a {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .menu a:hover {
            background-color: #2980b9;
        }
        .card {
            display: inline-block;
            width: 30%;
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>

    <div class="menu">
        <a href="manage_rooms.php">Manage Rooms</a>
        <a href="manage_schedule.php">Room Schedule</a>
    </div>

    <div class="card">
        <h3>Total Rooms</h3>
        <?php
        $stmt = $db->query("SELECT COUNT(*) AS total_rooms FROM rooms");
        $row = $stmt->fetch();
        echo "<p>" . $row['total_rooms'] . "</p>";
        ?>
    </div>

    <div class="card">
        <h3>Total Bookings</h3>
        <?php
        $stmt = $db->query("SELECT COUNT(*) AS total_bookings FROM bookings");
        $row = $stmt->fetch();
        echo "<p>" . $row['total_bookings'] . "</p>";
        ?>
    </div>

    <div class="card">
        <h3>Manage Users</h3>
        <a href="manage_users.php">View Users</a>
    </div>
</div>

</body>
</html>
