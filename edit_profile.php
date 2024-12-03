<?php
// edit_profile.php
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
    // Redirect if user is not found in the database
    header('Location: login.php');
    exit();
}

$errors = [];
$success = false;

// Handle form submission (update user profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $profile_picture = $user['profile_picture']; // Retain existing profile picture unless updated

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($file_extension), $valid_extensions)) {
            $new_file_name = uniqid() . '.' . $file_extension;
            $upload_path = 'uploads/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Remove the old profile picture if a new one was uploaded
                if ($user['profile_picture'] && $user['profile_picture'] !== 'default.png') {
                    unlink('uploads/' . $user['profile_picture']);
                }
                $profile_picture = $new_file_name;
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        } else {
            $errors[] = "Invalid file type for profile picture.";
        }
    }

    // Update the user profile (excluding username and email)
    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$name, $profile_picture, $user_id]);

        $success = true;
        header('Location: profile.php'); // Redirect to profile page after successful update
        exit();
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
        /* Basic styling for the edit profile page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
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
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
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
            background-color: #0056b3;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        .readonly-field {
            background-color: #f0f0f0;
            color: #333;
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

        <label for="profile_picture">Profile Picture (optional)</label>
        <input type="file" name="profile_picture" id="profile_picture">

        <button type="submit">Save Changes</button>
    </form>
</main>

</body>
</html>
