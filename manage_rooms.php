<?php
session_start();
include 'db.php';
include 'header.php';
include 'sidebar.php';

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
            // Insert default timeslots (set as 'available')
            for ($i = 9; $i <= 17; $i++) {
                $start_time = date("Y-m-d H:i:s", strtotime("today $i:00"));
                $end_time = date("Y-m-d H:i:s", strtotime("today " . ($i + 1) . ":00"));
                $timeslot_query = "INSERT INTO timeslots (room_id, start_time, end_time, status) VALUES (?, ?, ?, 'available')";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            padding-top: 70px; /* Ensure there's space for the sticky header */
        }
        

        /* Sidebar Styling */
        aside {
            width: 20%;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            height: 100vh; /* Ensure sidebar takes full height */
            position: fixed;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 30px;
        }

        .menu a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .menu a.active, .menu a:hover {
            background-color: #f2f2f2;
        }

        main {
            width: 80%;
            padding: 30px;
            margin-left: 20%;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #2575fc;
        }

        .form-container {
            background: #ffffff;  
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .room-list {
            background: #ffffff;  
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .room-list ul li a {
            color: #3498db;
            text-decoration: none;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Sidebar (included from sidebar.php) -->
    <!-- Main Content -->
    <main>
        <div class="container">
            <h1>Manage Rooms</h1>

            <!-- Add Room Form -->
            <div class="form-container">
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
            </div>

            <!-- Room List -->
            <div class="room-list">
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
            </div>
        </div>
    </main>

</body>
</html>
