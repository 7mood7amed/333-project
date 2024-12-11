<?php
session_start();
require('db.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = $db->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

if (!$user) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = false;
$profile_picture = $user['profile_picture'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_picture'])) {
        // Handle profile picture removal
        $upload_dir = 'uploads/';
        if ($profile_picture && $profile_picture !== 'default.png') {
            unlink($upload_dir . $profile_picture); // Remove current profile picture
        }
        $profile_picture = 'default.png'; // Reset to default
    } else {
        $name = trim(htmlspecialchars($_POST['name']));

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['profile_picture']['name'];
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 5 * 1024 * 1024; // 5MB max size

            if ($_FILES['profile_picture']['size'] > $max_file_size) {
                $errors[] = "Profile picture size should be less than 5MB.";
            } elseif (in_array(strtolower($file_extension), $valid_extensions)) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    if ($profile_picture && $profile_picture !== 'default.png') {
                        unlink($upload_dir . $profile_picture);
                    }
                    $profile_picture = $new_file_name;
                } else {
                    $errors[] = "Failed to upload profile picture.";
                }
            } else {
                $errors[] = "Invalid file type for profile picture.";
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare("UPDATE users SET name = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$name, $profile_picture, $user_id]);
            $success = true;
            header('Location: profile.php?success=true');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Room Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 1.8rem;
            color: #2575fc;
            margin-bottom: 20px;
        }

        label {
            text-align: left;
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        input.readonly-field {
            background-color: #f9f9f9;
            color: #666;
        }

        button {
            background: #2575fc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .remove-button {
            background: #dc3545;
            color: white;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-button:hover {
            background: #c82333;
        }

        .success-message {
            color: #28a745;
            margin-bottom: 15px;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 10px 0;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Profile</h1>

    <?php if ($success): ?>
        <p class="success-message">Profile updated successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <p class="error-message"><?= implode('<br>', $errors); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="username">Username (Cannot be changed)</label>
        <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" class="readonly-field" readonly>

        <label for="email">Email (Cannot be changed)</label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="readonly-field" readonly>

        <label for="profile_picture">Profile Picture</label>
        <?php if ($profile_picture !== 'default.png'): ?>
            <img src="uploads/<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture">
        <?php endif; ?>
        <input type="file" id="profile_picture" name="profile_picture">

        <button type="submit">Save Changes</button>
    </form>

    <?php if ($profile_picture !== 'default.png'): ?>
        <form method="POST">
            <button type="submit" name="remove_picture" class="remove-button">Remove Profile Picture</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
