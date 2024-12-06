<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch room details and available timeslots
if (isset($_GET['id'])) {
    $room_id = (int) $_GET['id'];

    // Fetch room details
    $query = "SELECT * FROM rooms WHERE id = ?";
    $statement = $db->prepare($query);
    $statement->execute([$room_id]);
    $room = $statement->fetch();

    if (!$room) {
        die("Room not found.");
    }

    // Fetch available timeslots for the room
    $query = "SELECT * FROM timeslots WHERE room_id = ? AND NOT EXISTS (SELECT 1 FROM bookings WHERE timeslot_id = timeslots.id AND status = 'booked')";
    $statement = $db->prepare($query);
    $statement->execute([$room_id]);
    $timeslots = $statement->fetchAll();
} else {
    die("Room ID is required.");
}

$errors = [];
$success = false;

// Handle booking request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['timeslot_id'])) {
        $timeslot_id = (int) $_POST['timeslot_id'];

        // Check if the selected timeslot is already booked
        $query = "SELECT * FROM bookings WHERE timeslot_id = ? AND status = 'booked'";
        $statement = $db->prepare($query);
        $statement->execute([$timeslot_id]);
        if ($statement->rowCount() > 0) {
            $errors[] = "This timeslot is already booked.";
        } else {
            // Insert booking into the database
            $user_id = $_SESSION['user_id'];
            $query = "INSERT INTO bookings (user_id, room_id, timeslot_id) VALUES (?, ?, ?)";
            $statement = $db->prepare($query);
            if ($statement->execute([$user_id, $room_id, $timeslot_id])) {
                $success = true;
            } else {
                $errors[] = "Failed to book the room. Please try again.";
            }
        }
    } else {
        $errors[] = "No timeslot selected.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
    <style>
        /* Styling for the page */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 60%;
            margin: 0 auto;
        }
        .form-group {
            margin: 10px 0;
        }
        .form-group label {
            display: block;
        }
        .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .error, .success {
            margin: 10px 0;
            padding: 10px;
            color: white;
        }
        .error {
            background-color: #e74c3c;
        }
        .success {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Book Room: <?php echo htmlspecialchars($room['name']); ?></h1>

    <?php if ($success): ?>
        <div class="success">Room booked successfully! Your booking is now confirmed.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error"><?php echo implode("<br>", $errors); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="timeslot_id">Select a timeslot</label>
            <select name="timeslot_id" id="timeslot_id" required>
                <option value="">-- Choose a timeslot --</option>
                <?php foreach ($timeslots as $timeslot): ?>
                    <option value="<?php echo $timeslot['id']; ?>">
                        <?php echo date("Y-m-d H:i", strtotime($timeslot['start_time'])) . " - " . date("Y-m-d H:i", strtotime($timeslot['end_time'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Book Now</button>
    </form>
</div>

</body>
</html>
