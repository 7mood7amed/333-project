<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$room_id = (int) $_GET['id'];

// Delete the room
$query = "DELETE FROM rooms WHERE id = ?";
$statement = $db->prepare($query);
if ($statement->execute([$room_id])) {
    header("Location: manage_rooms.php");
    exit();
} else {
    echo "Failed to delete the room.";
}
