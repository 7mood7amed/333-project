<?php
include 'db.php';
include 'header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$errors = [];
$successMessage = '';
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
    }

    // Check if the start time is before the end time
    if ($start_time >= $end_time) {
        $errors[] = "Start time must be earlier than end time.";
    }

    // Check if the room is already booked for this time slot
    if (empty($errors)) {
        $check_query = "SELECT * FROM schedules WHERE room_id = ? AND booking_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$room_id, $booking_date, $end_time, $start_time, $start_time, $end_time]);
        $existing_schedule = $check_stmt->fetch();

        if ($existing_schedule) {
            $errors[] = "This room is already booked for the selected time.";
        }
    }

    // If no errors, insert the schedule into the database
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO schedules (room_id, booking_date, start_time, end_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$room_id, $booking_date, $start_time, $end_time])) {
            $successMessage = "Schedule added successfully!";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
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
            align-items: center;
            padding: 20px;
        }
        .header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            width: 100%;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-container button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #2980b9;
        }
        .message {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        table {
            width: 80%;
            max-width: 800px;
            margin-top: 30px;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #3498db;
            color: white;
        }
        table td {
            background-color: #fff;
        }
        table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .table-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Manage Room Schedule</h1>
    </div>

    <!-- Success/Error Messages -->
    <div class="message">
        <?php if (!empty($errors)): ?>
            <p class="error-message"><?= implode('<br>', $errors); ?></p>
        <?php elseif ($successMessage): ?>
            <p class="success-message"><?= $successMessage; ?></p>
        <?php endif; ?>
    </div>

    <!-- Add Schedule Form -->
    <div class="form-container">
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
    </div>

    <!-- Existing Schedules Table -->
    <div class="table-container">
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
</div>

</body>
</html>
