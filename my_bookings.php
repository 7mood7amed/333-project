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
    <link rel="stylesheet" href="style-index.css">
    <style>
        /* Add any additional styles you may need for this page */
        .container {
            display: flex;
            flex-direction: column;
            padding: 30px;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .booking-table th, .booking-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .booking-table th {
            background-color: #f2f2f2;
        }

        .cancel-button {
            color: #e74c3c;
            text-decoration: none;
        }

        .cancel-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h1>Your Bookings</h1>
</header>

<div class="container">

    <!-- Display the user's bookings -->
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
        <p>You have no bookings.</p>
    <?php endif; ?>

</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
