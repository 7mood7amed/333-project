<?php
session_start(); // Start the session

if (isset($_SESSION['activeUser'])) {
    // Assuming $db is already initialized and connected to your database
    $query = "SELECT * FROM rooms";
    
    $result = $db->query($query); // Execute the query
    $rows = $result->fetchAll(); // Fetch all rows at once
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browsing</title>
</head>
<body>
    <div id="rooms">
        <?php
        if (isset($rows) && count($rows) > 0) { // Check if there are rooms available
            foreach ($rows as $row) {
        ?>
            <a href="roomDetails.php?room_id=<?php echo htmlspecialchars($row['id']); ?>">
                <?php echo htmlspecialchars($row['name']); ?> <!-- Display room name -->
            </a><br>
        <?php
            }
        } else {
            echo "No rooms available."; // Message if no rooms are found
        }
        ?>
    </div>
</body>
</html>

