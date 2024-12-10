<?php
session_start();
include 'db.php';
include 'header.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Unauthorized access.");
}

// Handle adding a new room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_name = htmlspecialchars($_POST['room_name']);
    $capacity = (int)$_POST['capacity'];
    $equipment = htmlspecialchars($_POST['equipment']);
    $status = $_POST['status'];

    if (empty($room_name) || empty($capacity) || empty($equipment) || empty($status)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO rooms (room_name, capacity, equipment, status) VALUES (?, ?, ?, ?)";
        $statement = $db->prepare($query);
        if ($statement->execute([$room_name, $capacity, $equipment, $status])) {
            $success = "Room added successfully.";
        } else {
            $errors[] = "Failed to add room. Please try again.";
        }
    }
}

// Handle changing room availability
if (isset($_GET['change_status']) && isset($_GET['id'])) {
    $room_id = (int)$_GET['id'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Room Booking System</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #2575fc;
        }

        .form-container {
            margin-bottom: 20px;
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

        .room-list ul {
            list-style: none;
            padding: 0;
        }

        .room-list ul li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .room-list ul li:last-child {
            border-bottom: none;
        }

        .room-list ul li a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .room-list ul li a:hover {
            color: #0056b3;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .message.success {
            background-color: #28a745;
            color: #fff;
        }

        .message.error {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>

<aside>
    <div class="logo">Admin Panel</div>
    <div class="menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_rooms.php" class="active">Manage Rooms</a>
        <a href="manage_schedule.php">Room Schedule</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="logout.php">Logout</a>
    </div>
</aside>

<main>
    <div class="container">
        <h1>Manage Rooms</h1>

        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Add Room Form -->
        <div class="form-container">
            <h2>Add New Room</h2>
            <form method="POST">
                <input type="text" name="room_name" placeholder="Room Name" required>
                <input type="number" name="capacity" placeholder="Capacity" required>
                <input type="text" name="equipment" placeholder="Equipment" required>
                <select name="status" required>
                    <option value="available">Available</option>
                    <option value="not available">Not Available</option>
                </select>
                <button type="submit" name="add_room">Add Room</button>
            </form>
        </div>

        <!-- Room List -->
        <div class="room-list">
            <h2>Existing Rooms</h2>
            <ul>
                <?php foreach ($rooms as $room): ?>
                    <li>
                        <?= htmlspecialchars($room['room_name']) ?> - <?= htmlspecialchars($room['status']) ?>
                        <a href="?change_status=true&id=<?= $room['id'] ?>&status=<?= $room['status'] ?>">Change Status</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</main>
</body>
</html>
