<?php
session_start();
include 'db.php';
include 'header.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch users from the database
$query = "SELECT * FROM users";
$statement = $db->prepare($query);
$statement->execute();
$users = $statement->fetchAll();

// Handle user status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $user_id = (int)$_POST['user_id'];
    $new_status = $_POST['status'];

    $updateQuery = "UPDATE users SET status = :status WHERE id = :user_id";
    $updateStmt = $db->prepare($updateQuery);
    if ($updateStmt->execute([':status' => $new_status, ':user_id' => $user_id])) {
        $status_message = "User status updated successfully!";
    } else {
        $status_error = "Failed to update user status. Please try again.";
    }
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $delete_user_id = (int)$_GET['delete_user'];

    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $deleteStmt = $db->prepare($deleteQuery);
    if ($deleteStmt->execute([$delete_user_id])) {
        $delete_message = "User deleted successfully!";
    } else {
        $delete_error = "Failed to delete user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
            width: 80%;
            margin-left: 20%;
            padding: 20px;
        }

        h1 {
            font-size: 2rem;
            color: #2575fc;
            text-align: center;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .user-table th, .user-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .user-table th {
            background-color: #2575fc;
            color: white;
        }

        .user-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-select {
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            max-width: 150px;
        }

        button[type="submit"] {
            background-color: #2575fc;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .action-buttons a {
            text-decoration: none;
            color: #e74c3c;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .action-buttons a:hover {
            color: #c0392b;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        .success {
            background-color: #28a745;
            color: white;
        }

        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<aside>
    <div class="logo">Admin Panel</div>
    <div class="menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_rooms.php">Manage Rooms</a>
        <a href="manage_schedule.php">Room Schedule</a>
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="logout.php">Logout</a>
    </div>
</aside>

<main>
    <h1>Manage Users</h1>

    <!-- Success and Error Messages -->
    <?php if (isset($status_message)): ?>
        <div class="message success"><?= htmlspecialchars($status_message) ?></div>
    <?php elseif (isset($status_error)): ?>
        <div class="message error"><?= htmlspecialchars($status_error) ?></div>
    <?php endif; ?>

    <?php if (isset($delete_message)): ?>
        <div class="message success"><?= htmlspecialchars($delete_message) ?></div>
    <?php elseif (isset($delete_error)): ?>
        <div class="message error"><?= htmlspecialchars($delete_error) ?></div>
    <?php endif; ?>

    <!-- User Table -->
    <table class="user-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="POST">
                            <select name="status" class="status-select">
                                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="admin" <?= $user['status'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" name="update_status">Update Status</button>
                        </form>
                    </td>
                    <td class="action-buttons">
                        <a href="manage_users.php?delete_user=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

</body>
</html>
