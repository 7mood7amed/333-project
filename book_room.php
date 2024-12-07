<?php
session_start();
include 'db.php';
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

    // Check if the room is already booked during the selected time
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

// Handle booking cancellation
if (isset($_GET['cancel_booking'])) {
    $booking_id = (int) $_GET['cancel_booking'];
    
    // Cancel the booking
    $query = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $statement = $db->prepare($query);
    $statement->execute([$booking_id, $_SESSION['user_id']]);
    
    if ($statement->rowCount() > 0) {
        $cancel_message = "Booking canceled successfully.";
    } else {
        $cancel_error = "Failed to cancel the booking.";
    }
}

// Fetch the user's current bookings
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
    <link rel="stylesheet" href="style-index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            padding: 30px;
        }
        header h1 {
            font-size: 32px;
            color: #3498db;
            margin-bottom: 20px;
        }
        .message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 8px;
            font-size: 16px;
        }
        .success {
            background-color: #28a745;
            color: #fff;
        }
        .error {
            background-color: #dc3545;
            color: #fff;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .form-container select,
        .form-container input,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #2980b9;
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
        }
        .booking-table th,
        .booking-table td {
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
    <h1>Book a Room</h1>
</header>

<div class="container">

    <!-- Display success or error messages -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (isset($cancel_message)): ?>
        <div class="message success"><?php echo $cancel_message; ?></div>
    <?php elseif (isset($cancel_error)): ?>
        <div class="message error"><?php echo $cancel_error; ?></div>
    <?php endif; ?>

    <!-- Booking Form -->
    <div class="form-container">
        <h2>Select a Room to Book</h2>
        <form action="" method="POST">
            <label for="room_id">Room</label>
            <select name="room_id" id="room_id" required>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['room_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="start_time">Start Time</label>
            <input type="datetime-local" name="start_time" id="start_time" required>

            <label for="end_time">End Time</label>
            <input type="datetime-local" name="end_time" id="end_time" required>

            <button type="submit" name="book_room">Book Room</button>
        </form>
    </div>

    <h2>Your Current Bookings</h2>
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
        <p>You have no current bookings.</p>
    <?php endif; ?>

</div>

<footer>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</body>
</html>
