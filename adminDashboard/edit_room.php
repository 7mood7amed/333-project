<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$room_id = (int) $_GET['id'];

// Fetch the room details
$query = "SELECT * FROM rooms WHERE id = ?";
$statement = $db->prepare($query);
$statement->execute([$room_id]);
$room = $statement->fetch();

if (!$room) {
    die("Room not found.");
}

$errors = [];
$success = false;

// Handle room edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = htmlspecialchars($_POST['room_name']);
    $capacity = (int) $_POST['capacity'];
    $equipment = htmlspecialchars($_POST['equipment']);

    if (empty($room_name) || empty($capacity) || empty($equipment)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $query = "UPDATE rooms SET room_name = ?, capacity = ?, equipment = ? WHERE id = ?";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_name, $capacity, $equipment, $room_id])) {
            $success = true;
        } else {
            $errors[] = "Failed to update room. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <style>
        /* Same styling as before */
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Room</h1>

    <?php if ($success): ?>
        <div class="success">
            <p>Room updated successfully.</p>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error">
            <p><?php echo implode('<br>', $errors); ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="room_name">Room Name:</label>
        <input type="text" id="room_name" name="room_name" value="<?php echo $room['room_name']; ?>" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo $room['capacity']; ?>" required>

        <label for="equipment">Equipment:</label>
        <input type="text" id="equipment" name="equipment" value="<?php echo $room['equipment']; ?>" required>

        <button type="submit">Update Room</button>
    </form>
</div>

</body>
</html>
