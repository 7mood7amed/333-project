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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Container */
        .container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styling */
        aside {
            width: 20%;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }
        aside .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 30px;
        }
        aside .menu a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        aside .menu a.active, aside .menu a:hover {
            background-color: #f2f2f2;
        }
        aside .menu a i {
            margin-right: 10px;
        }

        /* Main Section */
        main {
            width: 80%;
            padding: 30px;
            overflow-y: auto;
        }
        main h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .dashboard-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            flex: 1;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card i {
            font-size: 30px;
            color: #3498db;
            margin-bottom: 10px;
        }
        .card h3 {
            font-size: 20px;
            margin: 10px 0;
        }
        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #444;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <aside>
        <div class="logo">C <span style="color: #e74c3c;">BABAR</span></div>
        <div class="menu">
            <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_rooms.php"><i class="fas fa-door-open"></i> Manage Rooms</a>
            <a href="manage_schedule.php"><i class="fas fa-calendar-alt"></i> Room Schedule</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <h1>Admin Dashboard</h1>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <i class="fas fa-door-open"></i>
                <h3>Total Rooms</h3>
                <?php
                $stmt = $db->query("SELECT COUNT(*) AS total_rooms FROM rooms");
                $row = $stmt->fetch();
                echo "<p>" . $row['total_rooms'] . "</p>";
                ?>
            </div>
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Total Bookings</h3>
                <?php
                $stmt = $db->query("SELECT COUNT(*) AS total_bookings FROM bookings");
                $row = $stmt->fetch();
                echo "<p>" . $row['total_bookings'] . "</p>";
                ?>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Manage Users</h3>
                <a href="manage_users.php" style="color: #3498db;">View Users</a>
            </div>
        </div>
    </main>
</div>

</body>
</html>
