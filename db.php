<?php
// Database configuration
$host = 'localhost'; // Database host
$dbname = 'system_room_booking'; // Replace with your database name
$username = 'root'; // Default XAMPP MySQL username
$password = ''; // Default XAMPP MySQL password (usually empty)

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
