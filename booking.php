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
    <title>Book Room</title>
</head>
<body>
    <h2>Book <?= htmlspecialchars($room['room_name']) ?></h2>
    <form method="POST">
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">
        <label for="booking_time">Booking Time:</label>
        <input type="datetime-local" name="booking_time" required>
        <label for="duration">Duration (in minutes):</label>
        <input type="number" name="duration" required>
        <button type="submit">Book Now</button>
    </form>
</body>
</html>