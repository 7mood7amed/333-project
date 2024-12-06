<?php
include 'db.php';
session_start();
 
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}
 
$errors = [];
$schedules = [];
 
// Handle form submission for adding a schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = htmlspecialchars($_POST['room_id']);
    $booking_date = htmlspecialchars($_POST['booking_date']);
    $start_time = htmlspecialchars($_POST['start_time']);
    $end_time = htmlspecialchars($_POST['end_time']);
    // Validate input
    if (empty($room_id) || empty($booking_date) || empty($start_time) || empty($end_time)) {
        $errors[] = "All fields are required.";
    } else {
        // Insert the schedule into the database
        $stmt = $db->prepare("INSERT INTO schedules (room_id, booking_date, start_time, end_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$room_id, $booking_date, $start_time, $end_time])) {
            echo "Schedule added successfully!";
        } else {
            $errors[] = "Error adding schedule.";
        }
    }
}
 
// Fetch existing schedules
$stmt = $db->query("SELECT s.id, r.room_name, s.booking_date, s.start_time, s.end_time 
                     FROM schedules s 
                     JOIN rooms r ON s.room_id = r.id");
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Schedule</title>
<link rel="stylesheet" href="style.css"> <!-- Link to the common stylesheet -->
</head>
<body>
<div class="container">
<h1>Manage Room Schedule</h1>
<?php if (!empty($errors)): ?>
<p class="error-message" style="color: red;"><?= implode('<br>', $errors) ?></p>
<?php endif; ?>
 
    <h2>Add Schedule</h2>
<form method="POST">
<label for="room_id">Room:</label>
<select name="room_id" required>
<?php
            // Fetch rooms for the dropdown
            $room_stmt = $db->query("SELECT id, room_name FROM rooms");
            $rooms = $room_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rooms as $room) {
                echo "<option value=\"{$room['id']}\">{$room['room_name']}</option>";
            }
            ?>
</select>
 
        <label for="booking_date">Booking Date:</label>
<input type="date" name="booking_date" required>
 
        <label for="start_time">Start Time:</label>
<input type="time" name="start_time" required>
 
        <label for="end_time">End Time:</label>
<input type="time" name="end_time" required>
 
        <button type="submit">Add Schedule</button>
</form>
 
    <h2>Current Schedules</h2>
<table>
<thead>
<tr>
<th>ID</th>
<th>Room</th>
<th>Booking Date</th>
<th>Start Time</th>
<th>End Time</th>
</tr>
</thead>
<tbody>
<?php foreach ($schedules as $schedule): ?>
<tr>
<td><?= htmlspecialchars($schedule['id']) ?></td>
<td><?= htmlspecialchars($schedule['room_name']) ?></td>
<td><?= htmlspecialchars($schedule['booking_date']) ?></td>
<td><?= htmlspecialchars($schedule['start_time']) ?></td>
<td><?= htmlspecialchars($schedule['end_time']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>
</html>