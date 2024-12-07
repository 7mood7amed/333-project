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
        $profile_picture = $user['profile_picture'];

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
    <title>Edit Profile</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }
    .container {
        max-width: 500px;
        width: 100%;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    h1 {
        margin-bottom: 20px;
        color: #333;
    }
    label {
        display: block;
        margin-top: 10px;
        text-align: left;
    }
    input[type="text"],
    input[type="file"],
    input[type="email"] {
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }
    .readonly-field {
        background-color: #f0f0f0;
        color: #333;
    }
    button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        margin-top: 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background-color: #0056b3;
    }
    .error-message {
        color: red;
        margin-bottom: 15px;
    }
    .success-message {
        color: green;
        margin-bottom: 15px;
    }
    .remove-button {
        background-color: #dc3545;
        color: white;
        border: none;
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }
    .remove-button:hover {
        background-color: #c82333;
    }
</style>

</head>
<body>

<main class="container">
    <h1>Edit Profile</h1>

    <?php if ($success): ?>
        <p class="success-message">Profile updated successfully!</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <p class="error-message"><?php echo implode('<br>', $errors); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="username">Username (Cannot be changed)</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="readonly-field" readonly>

        <label for="email">Email (Cannot be changed)</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="readonly-field" readonly>

        <label for="profile_picture">Profile Picture</label>
        <?php if ($profile_picture !== 'default.png'): ?>
            <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 10px;">
        <?php endif; ?>
        <input type="file" name="profile_picture" id="profile_picture">

        <button type="submit">Save Changes</button>
    </form>

    <?php if ($profile_picture !== 'default.png'): ?>
        <form method="POST" style="margin-top: 10px;">
            <button type="submit" name="remove_picture" class="remove-button">Remove Profile Picture</button>
        </form>
    <?php endif; ?>
</main>

</body>
</html>
