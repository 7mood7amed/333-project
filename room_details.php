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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details - Room Booking System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
        }

        .room-details {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 800px;
        }

        .room-details h2 {
            font-size: 28px;
            color: #3498db;
            margin-bottom: 20px;
        }

        .room-details p {
            font-size: 18px;
            margin: 10px 0;
        }

        .room-details strong {
            color: #3498db;
        }

        .timeslot-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .timeslot-table th,
        .timeslot-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .timeslot-table th {
            background-color: #f2f2f2;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
                padding: 20px;
            }

            .room-details {
                width: 90%;
                padding: 20px;
            }

            .room-details h2 {
                font-size: 24px;
            }

            .room-details p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Room Details</h1>
</header>

<div class="container">
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
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
