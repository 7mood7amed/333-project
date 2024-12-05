<?php
session_start();
include 'db.php';

// Check if the room ID is provided
if (!isset($_GET['id'])) {
    die("Room ID is required.");
}

$room_id = (int) $_GET['id'];

// Fetch room details
$query = "SELECT * FROM rooms WHERE id = ?";
$statement = $db->prepare($query);
$statement->execute([$room_id]);
$room = $statement->fetch();

if (!$room) {
    die("Room not found.");
}

// Fetch available timeslots for the room
$query = "SELECT * FROM timeslots WHERE room_id = ? ORDER BY start_time";
$statement = $db->prepare($query);
$statement->execute([$room_id]);
$timeslots = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Details</title>
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
        .room-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .timeslot-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .timeslot-table th, .timeslot-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .timeslot-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<main class="container">
    <div class="room-details">
        <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> people</p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
        <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>

        <h3>Available Timeslots</h3>
        <?php if (count($timeslots) > 0): ?>
            <table class="timeslot-table">
                <thead>
                    <tr>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timeslots as $timeslot): ?>
                        <tr>
                            <td><?php echo date("Y-m-d H:i", strtotime($timeslot['start_time'])); ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($timeslot['end_time'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No available timeslots for this room.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>