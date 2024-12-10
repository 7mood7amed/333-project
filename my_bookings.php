<?php
session_start();
include 'db.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's bookings
$query = "SELECT b.*, r.room_name FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.user_id = ?";
$statement = $db->prepare($query);
$statement->execute([$_SESSION['user_id']]);
$user_bookings = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #2575fc;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .booking-table th, .booking-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .booking-table th {
            background-color: #2575fc;
            color: white;
            font-size: 1.1rem;
        }

        .booking-table td {
            color: #333;
        }

        .booking-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        .cancel-button {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .cancel-button:hover {
            transform: scale(1.05);
            color: #c0392b;
            text-decoration: underline;
        }

        p {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Bookings</h1>

    <?php if (count($user_bookings) > 0): ?>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($booking['start_time'])); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($booking['end_time'])); ?></td>
                        <td>
                            <a href="book_room.php?cancel_booking=<?php echo $booking['id']; ?>" class="cancel-button">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no bookings at the moment.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
