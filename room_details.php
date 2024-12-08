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

// Fetch available time slots for the room
$query = "
    SELECT s.id, s.booking_date, s.start_time, s.end_time 
    FROM schedules s 
    WHERE s.room_id = :room_id
    ORDER BY s.booking_date, s.start_time
";
$statement = $db->prepare($query);
$statement->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$statement->execute();
$schedules = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details - Room Booking System</title>
    <link rel="stylesheet" href="style-index.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            padding: 20px;
        }

        .header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            width: 100%;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .room-details {
            background: #fff;
            padding: 30px;
            width: 100%;
            max-width: 1200px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .room-details h2 {
            font-size: 28px;
            color: #3498db;
            margin-bottom: 20px;
        }

        .room-details p {
            font-size: 16px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 30px;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: white;
        }

        table td {
            background-color: #fff;
        }

        table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            display: flex;
            justify-content: center;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .room-details {
                padding: 15px;
                margin: 10px;
            }

            .room-details h2 {
                font-size: 24px;
            }

            table th, table td {
                padding: 10px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 15px;
                font-size: 20px;
            }

            table th, table td {
                padding: 8px;
                font-size: 12px;
            }

            .room-details h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Room Details</h1>
    </div>

    <div class="room-details">
        <h2><?php echo htmlspecialchars($room['room_name']); ?></h2>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> people</p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
        <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($room['status']); ?></p>

        <h3>Available Time Slots</h3>
        <?php if (count($schedules) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Booking Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['id']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['booking_date']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No available time slots for this room.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
