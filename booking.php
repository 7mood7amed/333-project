<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $booking_time = $_POST['booking_time'];
    $duration = $_POST['duration'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, booking_time, duration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $room_id, $booking_time, $duration]);
    header('Location: rooms.php');
}

$room_id = $_GET['room_id'];
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
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
    <h2>Book <?= $room['name'] ?></h2>
    <form method="POST">
        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
        <label for="booking_time">Booking Time:</label>
        <input type="datetime-local" name="booking_time" required>
        <label for="duration">Duration (in minutes):</label>
        <input type="number" name="duration" required>
        <button type="submit">Book Now</button>
    </form>
</body>
</html>