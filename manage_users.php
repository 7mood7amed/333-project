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

// Handle user status update (e.g., promoting to admin or deactivating user)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $user_id = (int) $_POST['user_id'];
    $new_status = $_POST['status'];

    // Update user status (e.g., promote to admin or deactivate account)
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
    $delete_user_id = (int) $_GET['delete_user'];

    // Delete user from the database
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fd;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Main Content */
        main {
            padding: 30px;
        }

        h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #3498db;
        }

        /* User Table Styling */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .user-table th {
            background-color: #3498db;
            color: white;
        }

        .user-table td {
            background-color: #fff;
        }

        .user-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        /* Action Buttons */
        .action-buttons a {
            text-decoration: none;
            color: #e74c3c;
            margin-right: 10px;
            font-size: 16px;
        }

        .action-buttons a:hover {
            color: #c0392b;
        }

        /* Status Update Form */
        .status-select {
            padding: 8px;
            font-size: 16px;
            width: 100%;
            max-width: 120px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-right: 10px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Success and Error Messages */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-table, .status-select {
                font-size: 14px;
            }

            button[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<main>
    <h1>Manage Users</h1>

    <!-- Success and Error Messages -->
    <?php if (isset($status_message)): ?>
        <div class="message success"><?php echo $status_message; ?></div>
    <?php elseif (isset($status_error)): ?>
        <div class="message error"><?php echo $status_error; ?></div>
    <?php endif; ?>

    <?php if (isset($delete_message)): ?>
        <div class="message success"><?php echo $delete_message; ?></div>
    <?php elseif (isset($delete_error)): ?>
        <div class="message error"><?php echo $delete_error; ?></div>
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
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form action="manage_users.php" method="POST">
                            <select name="status" class="status-select">
                                <option value="active" <?php echo ($user['status'] === 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo ($user['status'] === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                <option value="admin" <?php echo ($user['status'] === 'admin' ? 'selected' : ''); ?>>Admin</option>
                            </select>
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="update_status">Update Status</button>
                        </form>
                    </td>
                    <td class="action-buttons">
                        <a href="manage_users.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

</body>
</html>
