<?php
session_start();
include 'db.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Unauthorized access.");
}

// Handle adding a new room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_name = htmlspecialchars($_POST['room_name']);
    $capacity = (int) $_POST['capacity'];
    $equipment = htmlspecialchars($_POST['equipment']);
    $status = $_POST['status'];

    if (empty($room_name) || empty($capacity) || empty($equipment) || empty($status)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO rooms (room_name, capacity, equipment, status) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_name, $capacity, $equipment, $status])) {
            $room_id = $db->lastInsertId();
            // Insert default timeslots
            for ($i = 9; $i <= 17; $i++) {
                $start_time = date("Y-m-d H:i:s", strtotime("today $i:00"));
                $end_time = date("Y-m-d H:i:s", strtotime("today " . ($i + 1) . ":00"));
                $timeslot_query = "INSERT INTO timeslots (room_id, start_time, end_time) VALUES (?, ?, ?)";
                $timeslot_statement = $db->prepare($timeslot_query);
                $timeslot_statement->execute([$room_id, $start_time, $end_time]);
            }
            $success = true;
        } else {
            $errors[] = "Failed to add room. Please try again.";
        }
    }
}

// Handle changing room availability
if (isset($_GET['change_status']) && isset($_GET['id'])) {
    $room_id = (int) $_GET['id'];
    $new_status = $_GET['status'] === 'available' ? 'not available' : 'available';

    $update_query = "UPDATE rooms SET status = ? WHERE id = ?";
    $update_statement = $db->prepare($update_query);
    if ($update_statement->execute([$new_status, $room_id])) {
        header("Location: manage_rooms.php");
        exit();
    } else {
        $errors[] = "Failed to update room status.";
    }
}

// Fetch all rooms for display
$query = "SELECT * FROM rooms";
$statement = $db->prepare($query);
$statement->execute();
$rooms = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
</head>
<body>
    <h1>Manage Rooms</h1>
    <form method="POST">
        <input type="text" name="room_name" placeholder="Room Name" required>
        <input type="number" name="capacity" placeholder="Capacity" required>
        <input type="text" name="equipment" placeholder="Equipment" required>
        <select name="status">
            <option value="available">Available</option>
            <option value="not available">Not Available</option>
        </select>
        <button type="submit" name="add_room">Add Room</button>
    </form>

    <h2>Existing Rooms</h2>
    <ul>
        <?php foreach ($rooms as $room): ?>
            <li>
                <?php echo htmlspecialchars($room['room_name']); ?> - 
                <?php echo htmlspecialchars($room['status']); ?> 
                <a href="?change_status=true&id=<?php echo $room['id']; ?>&status=<?php echo $room['status']; ?>">Change Status</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>