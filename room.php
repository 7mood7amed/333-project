<?php
session_start();
include 'db.php';

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
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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
        }
    </style>
</head>
<body>
<main class="container">
    <h1>Available Rooms</h1>
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