<?php
session_start();
include 'db.php';

// Fetch all rooms
$query = "SELECT * FROM rooms";
$statement = $db->prepare($query);
$statement->execute();
$rooms = $statement->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browsing</title>
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
        h1 {
            text-align: center;
        }
        .room-list {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .room-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .room-card h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .room-card p {
            margin: 10px 0;
        }
        .room-card a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .room-card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<main class="container">
    <h1>Available Rooms</h1>

    <div class="room-list">
        <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <h2><?php echo htmlspecialchars($room['name']); ?></h2>
                <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> people</p>
                <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
                <a href="room_details.php?id=<?php echo $room['id']; ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>
