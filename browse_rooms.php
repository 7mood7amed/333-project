<?php
session_start();
include 'db.php';
include 'header.php';

// Fetch all available rooms from the database
$query = "SELECT * FROM rooms WHERE status = 'available'";
$statement = $db->prepare($query);
$statement->execute();
$rooms = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Rooms - Room Booking System</title>
    <link rel="stylesheet" href="style-index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            text-align: center;
            color: #3498db;
        }
        .room-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .room-card {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .room-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }
        .room-card h2 {
            font-size: 24px;
            color: #3498db;
            margin-bottom: 15px;
        }
        .room-card p {
            font-size: 16px;
            color: #333;
        }
        .room-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .room-card a:hover {
            background-color: #2980b9;
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #2c3e50;
            color: white;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<header>
    <h1>Browse Available Rooms</h1>
</header>

<div class="container">
    <!-- Room Browsing Section -->
    <div class="room-list">
        <?php if (count($rooms) > 0): ?>
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> people</p>
                    <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>
                    <a href="room_details.php?id=<?php echo $room['id']; ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No rooms available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
