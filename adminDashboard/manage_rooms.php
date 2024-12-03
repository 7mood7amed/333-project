<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = false;

// Adding a new room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_name = htmlspecialchars($_POST['room_name']);
    $capacity = (int) $_POST['capacity'];
    $equipment = htmlspecialchars($_POST['equipment']);
    
    // Validate room inputs
    if (empty($room_name) || empty($capacity) || empty($equipment)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO rooms (room_name, capacity, equipment) VALUES (?, ?, ?)";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_name, $capacity, $equipment])) {
            $success = true;
        } else {
            $errors[] = "Failed to add room. Please try again.";
        }
    }
}

// Fetch all rooms
$rooms_query = "SELECT * FROM rooms";
$rooms_result = $db->query($rooms_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <style>
        /* Same styling as admin dashboard, but with additional styles for rooms */
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Rooms</h1>

    <?php if ($success): ?>
        <div class="success">
            <p>Room added successfully.</p>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error">
            <p><?php echo implode('<br>', $errors); ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h2>Add Room</h2>
        <label for="room_name">Room Name:</label>
        <input type="text" id="room_name" name="room_name" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" required>

        <label for="equipment">Equipment:</label>
        <input type="text" id="equipment" name="equipment" required>

        <button type="submit" name="add_room">Add Room</button>
    </form>

    <h2>Existing Rooms</h2>
    <table>
        <thead>
            <tr>
                <th>Room Name</th>
                <th>Capacity</th>
                <th>Equipment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($room = $rooms_result->fetch()): ?>
                <tr>
                    <td><?php echo $room['room_name']; ?></td>
                    <td><?php echo $room['capacity']; ?></td>
                    <td><?php echo $room['equipment']; ?></td>
                    <td>
                        <a href="edit_room.php?id=<?php echo $room['id']; ?>">Edit</a> |
                        <a href="delete_room.php?id=<?php echo $room['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
