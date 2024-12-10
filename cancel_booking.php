<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the booking ID is provided
if (!isset($_GET['booking_id'])) {
    die("Booking ID is required.");
}

$booking_id = (int) $_GET['booking_id'];

// Fetch the booking details
$query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
$statement = $db->prepare($query);
$statement->execute([$booking_id, $_SESSION['user_id']]);
$booking = $statement->fetch();

if (!$booking) {
    die("Booking not found or you are not authorized to cancel this booking.");
}

$errors = [];
$success = false;

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the status to 'cancelled'
    $query = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $statement = $db->prepare($query);
    if ($statement->execute([$booking_id])) {
        $success = true;
    } else {
        $errors[] = "Failed to cancel the booking. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #3498db;
        }
        .form-group {
            margin: 20px 0;
            text-align: center;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .form-group button:hover {
            background-color: #c0392b;
        }
        .error, .success {
            margin: 10px 0;
            padding: 10px;
            color: white;
            border-radius: 5px;
        }
        .error {
            background-color: #e74c3c;
        }
        .success {
            background-color: #2ecc71;
        }
        .details {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Cancel Booking</h1>

    <?php if ($success): ?>
        <div class="success">
            <p>Your booking has been successfully cancelled.</p>
            <a href="bookings.php">Go back to your bookings</a>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error">
            <p><?php echo implode('<br>', $errors); ?></p>
        </div>
    <?php else: ?>
        <div class="details">
            <h3>Booking Details</h3>
            <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['id']); ?></p>
            <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['room_id']); ?></p>
            <p><strong>Booking Time:</strong> <?php echo htmlspecialchars($booking['booking_time']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <p>Are you sure you want to cancel this booking?</p>
                <button type="submit">Yes, Cancel Booking</button>
            </div>
        </form>
        <div class="form-group">
            <a href="bookings.php">Cancel and go back to bookings</a>
        </div>
    <?php endif; ?>
</div>
<script src="js/scripts.js"></script>
</body>
</html>
