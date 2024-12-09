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
        }

        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #3498db;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            color: white;
        }

        .logo-img {
            height: 50px;
            animation: fadeInLeft 1.5s ease;
        }

        nav {
            flex-grow: 1;
            text-align: center;
        }

        nav a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        nav a:hover {
            transform: translateY(-3px);
            background-color: #2980b9;
            border-radius: 5px;
        }

        .user-options {
            display: flex;
            align-items: center;
        }

        .user-options a.button {
            margin-left: 10px;
            padding: 10px 15px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .user-options a.button:hover {
            background-color: #1a5276;
            transform: translateY(-3px);
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.1);
        }

        .logout-button {
            margin-left: 10px;
            padding: 10px 15px;
            background-color: #e74c3c;  /* Red color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c0392b;  /* Darker red on hover */
            transform: translateY(-3px);
        }

        .container {
            display: flex;
            flex-direction: column;
            padding: 40px 20px;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #2575fc;
            animation: bounceIn 1.5s ease;
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
            background: #ffffff;
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

        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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

        @keyframes bounceIn {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
             50% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>



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
