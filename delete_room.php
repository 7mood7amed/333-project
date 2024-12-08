<?php
session_start();
include 'db.php';
include 'header.php';


// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if the room ID is provided
if (!isset($_GET['id'])) {
    echo "Room ID is required.";
    exit();
}

$room_id = (int) $_GET['id'];

// Check if the room exists before attempting to delete
$query = "SELECT * FROM rooms WHERE id = ?";
$statement = $db->prepare($query);
$statement->execute([$room_id]);
$room = $statement->fetch();

if (!$room) {
    echo "Room not found.";
    exit();
}

// Delete the room
$query = "DELETE FROM rooms WHERE id = ?";
$statement = $db->prepare($query);
if ($statement->execute([$room_id])) {
    header("Location: manage_rooms.php?message=Room+deleted+successfully");
    exit();
} else {
    echo "Failed to delete the room. Please try again.";
}
