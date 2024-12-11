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
        $check_query = "SELECT * FROM schedules WHERE room_id = ? AND booking_date = ? 
                        AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?))";
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
    <title>Manage Schedule - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        aside {
            width: 20%;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            height: 100vh; /* Full-height sidebar */
            position: fixed; /* Fixed to the left */
            left: 0;
        }

        aside .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2575fc;
            text-align: center;
            margin-bottom: 30px;
        }

        aside .menu a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 1rem;
            color: #555;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        aside .menu a.active, aside .menu a:hover {
            background-color: #f2f2f2;
            color: #2575fc;
        }

        main {
            width: 80%; /* Take the remaining space */
            margin-left: 20%; /* Offset by sidebar width */
            padding: 20px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Space between elements */
        }

        .form-container, .table-container {
            flex: 1;
            min-width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #2575fc;
        }

        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #2575fc;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #2575fc;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .error-message {
            color: #dc3545;
        }

        .success-message {
            color: #28a745;
        }
    </style>
</head>
<body>

<aside>
    <div class="logo">Admin Panel</div>
    <div class="menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_rooms.php">Manage Rooms</a>
        <a href="manage_schedule.php" class="active">Room Schedule</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="logout.php">Logout</a>
    </div>
</aside>

<main>
    <h1>Manage Room Schedules</h1>

    <!-- Success/Error Messages -->
    <div class="message">
        <?php if (!empty($errors)): ?>
            <p class="error-message"><?= implode('<br>', $errors); ?></p>
        <?php elseif ($successMessage): ?>
            <p class="success-message"><?= $successMessage; ?></p>
        <?php endif; ?>
    </div>

    <div class="container">
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
            <h2>Existing Schedules</h2>
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
</main>

</body>
</html>
