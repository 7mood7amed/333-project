<?php
session_start();
include 'db.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle booking cancellation
if (isset($_GET['cancel_booking'])) {
    $booking_id = (int) $_GET['cancel_booking'];

    // Check if the booking belongs to the current user
    $query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
    $statement = $db->prepare($query);
    $statement->execute([$booking_id, $_SESSION['user_id']]);
    $booking = $statement->fetch();

    if ($booking) {
        // Cancel the booking
        $query = "DELETE FROM bookings WHERE id = ?";
        $statement = $db->prepare($query);
        if ($statement->execute([$booking_id])) {
            $success_message = "Your booking has been successfully cancelled.";
        } else {
            $error_message = "Failed to cancel the booking. Please try again.";
        }
    } else {
        $error_message = "Booking not found or you do not have permission to cancel this booking.";
    }

    // Redirect back to the bookings page after cancellation
    header("Location: my_bookings.php");
    exit();
}

// Fetch all available rooms
$query = "SELECT * FROM rooms WHERE status = 'available'";
$statement = $db->prepare($query);
$statement->execute();
$rooms = $statement->fetchAll();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_room'])) {
    $room_id = (int) $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $user_id = $_SESSION['user_id'];

    // Check if the room is already booked
    $query = "SELECT * FROM bookings WHERE room_id = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))";
    $statement = $db->prepare($query);
    $statement->execute([$room_id, $end_time, $start_time, $start_time, $end_time]);

    if ($statement->rowCount() > 0) {
        $error_message = "The room is already booked for this time slot.";
    } else {
        // Insert booking into the database
        $query = "INSERT INTO bookings (room_id, user_id, start_time, end_time) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_id, $user_id, $start_time, $end_time])) {
            $success_message = "Room booked successfully!";
        } else {
            $error_message = "Failed to book the room. Please try again.";
        }
    }
}

// Fetch user's current bookings
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
    <title>Book a Room - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #2575fc;
            margin-bottom: 20px;
        }

        .form-container,
        .booking-container {
            margin: 20px 0;
        }

        .form-container form,
        .booking-container table {
            width: 100%;
        }

        .form-container form input,
        .form-container form select,
        .form-container form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container form button {
            background-color: #2575fc;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container form button:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .message.success {
            background-color: #28a745;
            color: white;
        }

        .message.error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book a Room</h1>

        <?php if (isset($success_message)) : ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php elseif (isset($error_message)) : ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2>Booking Form</h2>
            <form method="POST">
                <label for="room_id">Room</label>
                <select id="room_id" name="room_id" required>
                    <?php foreach ($rooms as $room) : ?>
                        <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="start_time">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" required>
                <label for="end_time">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" required>
                <button type="submit" name="book_room">Book Room</button>
            </form>
        </div>

        <div class="booking-container">
            <h2>Your Bookings</h2>
            <?php if ($user_bookings) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_bookings as $booking) : ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['room_name']) ?></td>
                                <td><?= htmlspecialchars($booking['start_time']) ?></td>
                                <td><?= htmlspecialchars($booking['end_time']) ?></td>
                                <td>
                                    <a href="book_room.php?cancel_booking=<?= $booking['id'] ?>" class="cancel-button">Cancel</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No bookings available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
