<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../images/logo.png" alt="Logo" class="logo-icon">
                <h2>Admin Panel</h2>
            </div>
            <div class="user-panel">
            <img src="../images/user.png" id="profile-img" alt="Profile" onerror="this.src='../images/user.png';">
                <div class="profile-dropdown">
                    <span id="profile-name">Admin Name</span>
                    <div id="profile-options" class="dropdown-content">
                        <a href="#" id="edit-profile">Edit Profile</a>
                        <a href="#" id="logout">Logout</a>
                    </div>
                </div>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="#" data-page="notifications" class="menu-item"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="#" data-page="dashboard" class="menu-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#" data-page="manage-users" class="menu-item"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="#" data-page="Ticket-Management" class="menu-item"><i class="fas fa-file-alt"></i> Ticket Management</a></li>
                    <li><a href="#" data-page="settings" class="menu-item"><i class="fas fa-cogs"></i> Settings</a></li>
                    <li><a href="#" data-page="calendar" class="menu-item"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div id="content">
                <h1>Welcome to the Admin Dashboard</h1>
                <p>Select an option from the menu to see details.</p>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
