<?php
session_start();

if ($_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");  // If not staff, redirect to login page
    exit();
}
include '../db.php';

$staff_email = $_SESSION['email'];
$staff_role = $_SESSION['role'];

$sql = "SELECT * FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_email);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc(); // Assuming staff details are fetched successfully
$staff_name = $staff['name'];
$profile_picture = $staff['profile_picture'] ?? '../images/user.png';
$_SESSION['staff_id'] = $staff['staff_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="staff.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../images/logo.png" alt="Logo" class="logo-icon">
                <h2>Smart Helpdesk</h2>
            </div>
            <div class="user-panel">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profile-img" alt="Profile" onerror="this.src='../images/user.png';">
                <div class="profile-dropdown">
                    <div class="profile-info">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profile-img" alt="Profile" onerror="this.src='../images/user.png';">
                        <span id="profile-name"><?php echo htmlspecialchars($staff_name); ?></span> <!-- Display the staff's name -->
                        <a href="edit-profile.php" id="edit-profile-pic">Edit Profile Picture</a>
                    </div>
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
                    <li><a href="#" data-page="manage-tickets" class="menu-item"><i class="fas fa-ticket-alt"></i> Manage Tickets</a></li>
                    <li><a href="#" data-page="chat" class="menu-item"><i class="fas fa-comments"></i> Chat</a></li>
                    <li><a href="#" data-page="calendar" class="menu-item"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="content">
            <h1>Welcome to the Staff Dashboard</h1>
            <p>Select an option from the menu to get started.</p>
        </main>
    </div>

    <script src="staff.js"></script>
</body>
</html>
