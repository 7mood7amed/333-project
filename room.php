<?php
session_start();
include 'db.php';
include 'header.php';

// Fetch only available rooms
$query = "SELECT * FROM rooms WHERE status = 'available'";
$statement = $db->prepare($query);
$statement->execute();
$rooms = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Rooms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #ff6600;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            color: #fff;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .room-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .room-card:hover {
            transform: scale(1.05);
        }
        .room-card h2 {
            color: #ff6600;
            font-size: 24px;
        }
        .room-card a {
            text-decoration: none;
            color: #ff6600;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        .room-card a:hover {
            color: #000;
        }
    </style>
</head>
<body>
<header>
    <h1>Available Rooms</h1>
</header>
<main class="container">
    <div class="room-list">
        <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
                <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> people</p>
                <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
                <a href="roomDetails.php?id=<?php echo $room['id']; ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>
