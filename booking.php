<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $booking_time = $_POST['booking_time'];
    $duration = $_POST['duration'];
    $user_id = $_SESSION['user_id'];

    // Insert booking into the database
    $stmt = $db->prepare("INSERT INTO bookings (user_id, room_id, booking_time, duration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $room_id, $booking_time, $duration]);
    $_SESSION['flash_success'] = "Room booked successfully!";
    header('Location: rooms.php');
    exit();
}

$room_id = $_GET['room_id'];
$stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - Room Booking System</title>
    <link rel="stylesheet" href="style-index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 28px;
            color: #3498db;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 16px;
            margin: 8px 0;
        }
        input[type="datetime-local"],
        input[type="number"],
        button {
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="message success"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <h2>Book <?= htmlspecialchars($room['room_name']) ?></h2>

    <form method="POST">
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

        <label for="booking_time">Booking Time:</label>
        <input type="datetime-local" name="booking_time" required>

        <label for="duration">Duration (in minutes):</label>
        <input type="number" name="duration" required>

        <button type="submit">Book Now</button>
    </form>
</div>

</body>
</html>
