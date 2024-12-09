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
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #3498db;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            color: white;
        }

        .logo-img {
            height: 50px;
            animation: fadeInLeft 1.5s ease;
        }

        nav {
            flex-grow: 1;
            text-align: center;
        }

        nav a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        nav a:hover {
            transform: translateY(-3px);
            background-color: #2980b9;
            border-radius: 5px;
        }

        .user-options {
            display: flex;
            align-items: center;
        }

        .user-options a.button {
            margin-left: 10px;
            padding: 10px 15px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .user-options a.button:hover {
            background-color: #1a5276;
            transform: translateY(-3px);
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.1);
        }

        .logout-button {
            margin-left: 10px;
            padding: 10px 15px;
            background-color: #e74c3c;  /* Red color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c0392b;  /* Darker red on hover */
            transform: translateY(-3px);
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #2575fc;
            animation: bounceIn 1.5s ease;
        }

        .form-container {
            background: #ffffff;  
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            animation: fadeInUp 1.2s ease;
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

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            60% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    
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
</body>
</html>
