<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Sidebar Styling */
.container {
    display: flex;
    height: 100vh;
}

aside {
    width: 20%;
    background: #fff;
    padding: 20px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
}

aside .logo {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 30px;
}

aside .menu a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #555;
    font-size: 16px;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px;
    transition: background 0.3s;
}

aside .menu a.active, aside .menu a:hover {
    background-color: #f2f2f2;
}

aside .menu a i {
    margin-right: 10px;
}

main {
    width: 80%;
    padding: 30px;
    overflow-y: auto;
}


    </style>
</head>
<body>
  <!-- sidebar.php -->
<aside>
    <div class="logo">C <span style="color: #e74c3c;">BABAR</span></div>
    <div class="menu">
        <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_rooms.php"><i class="fas fa-door-open"></i> Manage Rooms</a>
        <a href="manage_schedule.php"><i class="fas fa-calendar-alt"></i> Room Schedule</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>



</body>
</html>