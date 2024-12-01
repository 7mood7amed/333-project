<?php
session_start(); // Start the session

if (isset($_GET['room_id'])) {
    $roomId = intval($_GET['room_id']); // Ensure room_id is an integer

    // Assuming $db is already initialized and connected to your database
    $query = "SELECT * FROM rooms WHERE id = :roomId"; // Use prepared statements to prevent SQL injection
    $stmt = $db->prepare($query);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $room = $stmt->fetch(); // Fetch the room details
} else {
    echo "Room ID not specified.";
    exit;
}

if (!$room) {
    echo "Room not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($room['name']); ?></h1>
    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?></p>
    <p><strong>Equipment:</strong> <?php echo htmlspecialchars($room['equipment']); ?></p>
    <p><strong>Available Timeslots:</strong></p>
    <ul>
        <?php
        // Assuming 'timeslots' is a field in your database containing available times
        $timeslots = json_decode($room['timeslots'], true); // Assuming timeslots are stored as JSON
        foreach ($timeslots as $timeslot) {
            echo "<li>" . htmlspecialchars($timeslot) . "</li>";
        }
        ?>
    </ul>
</body>
</html>