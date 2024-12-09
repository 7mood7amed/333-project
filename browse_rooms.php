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
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            animation: backgroundShift 10s infinite alternate;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #2575fc;
            font-size: 2.5rem;
            margin-bottom: 30px;
            animation: bounceIn 1.5s ease;
        }

        .room-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .room-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: zoomIn 1.2s ease;
        }

        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .room-card h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .room-card p {
            color: #666;
            margin-bottom: 10px;
        }

        .room-card a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #2575fc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .room-card a:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }

        /* Animations */
        @keyframes backgroundShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            60% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>


<div class="container">
    <h1>Browse Available Rooms</h1>
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
