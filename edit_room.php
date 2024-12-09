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
    $room_name = trim(htmlspecialchars($_POST['room_name']));
    $capacity = (int) $_POST['capacity'];
    $equipment = trim(htmlspecialchars($_POST['equipment']));

    // Validate inputs
    if (empty($room_name) || empty($capacity) || empty($equipment)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($room_name) < 3 || strlen($room_name) > 100) {
        $errors[] = "Room name must be between 3 and 100 characters.";
    }

    if ($capacity <= 0) {
        $errors[] = "Capacity must be a positive number.";
    }

    if (empty($errors)) {
        $query = "UPDATE rooms SET room_name = ?, capacity = ?, equipment = ? WHERE id = ?";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_name, $capacity, $equipment, $room_id])) {
            $success = true;
            header("Location: manage_rooms.php?success=true"); // Redirect to manage rooms page on success
            exit();
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
        /* Basic Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-align: center;
            display: block;
            margin: auto;
            text-decoration: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            margin: 10px 0;
            padding: 10px;
            color: white;
            background-color: #e74c3c;
            border-radius: 5px;
        }
        .success {
            margin: 10px 0;
            padding: 10px;
            color: white;
            background-color: #2ecc71;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Room</h1>

    <?php if ($success): ?>
        <div class="success">
            <p>Room updated successfully.</p>
            <a href="manage_rooms.php">Go back to manage rooms</a>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error">
            <p><?php echo implode('<br>', $errors); ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="room_name">Room Name:</label>
        <input type="text" id="room_name" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo $room['capacity']; ?>" required>

        <label for="equipment">Equipment:</label>
        <input type="text" id="equipment" name="equipment" value="<?php echo htmlspecialchars($room['equipment']); ?>" required>

        <button type="submit">Update Room</button>
    </form>
</div>
<script src="js/scripts.js"></script>
</body>
</html>
