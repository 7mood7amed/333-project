<?php
session_start();
include 'db.php';
include 'header.php';

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
    <title>Admin Dashboard - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            display: flex;
            margin: 0;
            min-height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
        }

        aside {
            width: 20%;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        aside .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2575fc;
            text-align: center;
            margin-bottom: 30px;
        }

        aside .menu a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 1rem;
            color: #555;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        aside .menu a.active, aside .menu a:hover {
            background-color: #f2f2f2;
            color: #2575fc;
        }

        main {
            width: 80%;
            padding: 30px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        main h1 {
            font-size: 2rem;
            color: #2575fc;
            margin-bottom: 20px;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .card i {
            font-size: 2.5rem;
            color: #2575fc;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1.5rem;
            color: #444;
            font-weight: bold;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .card a:hover {
            color: #0056b3;
        }

        footer {
            text-align: center;
            background: #2c3e50;
            color: white;
            padding: 15px 0;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            aside {
                width: 100%;
            }

            main {
                width: 100%;
                padding: 20px;
            }

            .dashboard-cards {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <aside>
        <div class="logo">Admin Panel</div>
        <div class="menu">
            <a href="#" class="active">Dashboard</a>
            <a href="manage_rooms.php">Manage Rooms</a>
            <a href="manage_schedule.php">Room Schedule</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="logout.php">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-cards">
            <!-- Total Rooms -->
            <div class="card">
                <i class="fas fa-door-open"></i>
                <h3>Total Rooms</h3>
                <?php
                $stmt = $db->query("SELECT COUNT(*) AS total_rooms FROM rooms");
                $row = $stmt->fetch();
                echo "<p>" . $row['total_rooms'] . "</p>";
                ?>
            </div>

            <!-- Total Bookings -->
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Total Bookings</h3>
                <?php
                $stmt = $db->query("SELECT COUNT(*) AS total_bookings FROM bookings");
                $row = $stmt->fetch();
                echo "<p>" . $row['total_bookings'] . "</p>";
                ?>
            </div>

            <!-- Manage Users -->
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Manage Users</h3>
                <a href="manage_users.php">View Users</a>
            </div>
        </div>
    </main>
</div>

</body>
</html>
