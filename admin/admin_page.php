<?php
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");  // If not user, redirect to login page
    exit();
}
include '../db.php';

$user_email = $_SESSION['email'];
$user_role = $_SESSION['role'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc(); // Assuming user details are fetched successfully
$user_name = $user['name'];
$profile_picture = $user['profile_picture']; // Fetch profile picture
$_SESSION['user_id'] = $user['user_id']


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
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
                <?php if ($profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profile-img" alt="Profile" onerror="this.src='../images/user.png';">
                <?php else: ?>
                    <img src="../images/user.png" id="profile-img" alt="Profile" onerror="this.src='../images/user.png';">
                <?php endif; ?>
                <div class="profile-dropdown">
                    <div class="profile-info">
                    <span id="profile-name" style="text-align: center; display: block; margin-left: 20px; color: orange; font-size: 20px;"><?php echo htmlspecialchars($user_name); ?></span> <!-- Display the user's name -->
                    <!-- Display the user's name -->
                    </div>
                    <div id="profile-options" class="dropdown-content">
                    <a href="#" id="edit-profile" style="display: block; text-align: center; margin: 0 auto;">Edit Profile</a>

                    <a href="#" id="logout" style="display: block; text-align: center; margin: 0 auto;">Logout</a>

                    </div>
                </div>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="admin_page.php?page=notifications" class="menu-item"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="admin_page.php?page=dashboard" class="menu-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_page.php?page=manage-users"  class="menu-item"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="admin_page.php?page=Ticket-Management"  class="menu-item"><i class="fas fa-file-alt"></i> Ticket Management</a></li>
                    <li><a href="admin_page.php?page=calendar"  class="menu-item"><i class="fas fa-calendar-alt"></i> Calendar</a></li>
                    <li><a href="admin_page.php?page=View-Feedback"  class="menu-item"><i class="fas fa-calendar-alt"></i> View-Feedback</a></li>

                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="content">
            <?php
            $page = $_GET['page'] ?? 'dashboard';
            $allowed_pages = ['notifications', 'dashboard', 'manage-users', 'Ticket-Management', 'calendar','View-Feedback'];
            if (in_array($page, $allowed_pages)) {
                include "$page.php";
            } else {
                echo "<h1>Welcome to the User Dashboard</h1>";
                echo "<p>Select an option from the menu to get started.</p>";
            }
            ?>
        </main>
        </div>
 <script src="admin.js"></script>

</body>
</html>
