<?php
session_start();

if ($_SESSION['role'] !== 'user') {
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
$profile_picture = $user['profile_picture'] ?? '../images/user.png';
$_SESSION['user_id'] = $user['user_id'];

// Add this function to handle rating
function addRating($ticket_id, $rating, $review) {
    global $conn;
    $sql = "INSERT INTO ticket_ratings (ticket_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $ticket_id, $_SESSION['user_id'], $rating, $review);
    return $stmt->execute();
}

// Add this section to handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['rating'], $_POST['review'])) {
    $ticket_id = $_POST['ticket_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    if (addRating($ticket_id, $rating, $review)) {
        // Send SMS notification after successful rating submission
        $message = "Thank you for your feedback! Your rating for ticket ID $ticket_id has been submitted successfully.";
        include 'send_sms.php'; // Include the SMS function from a separate file
        if (sendSMS($message, $_SESSION['phone_number'])) {
            echo "Rating submitted successfully and SMS sent.";
        } else {
            echo "Rating submitted successfully but failed to send SMS.";
        }
    } else {
        echo "Failed to submit rating.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="user.css">

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
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profile-img" alt="Profile"
                    onerror="this.src='../images/user.png';">
                <div class="profile-dropdown">
                    <div class="profile-info">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profile-img" alt="Profile"
                            onerror="this.src='../images/user.png';">
                        <span id="profile-name"
                            style="text-align: center; display: block; margin-left: 20px; color: orange; font-size: 20px;"><?php echo htmlspecialchars($user_name); ?></span>
                        <!-- Display the user's name -->
                        <!-- Display the user's name -->
                        <!-- Display the user's name -->

                    </div>
                    <div id="profile-options" class="dropdown-content">
                        <a href="#" id="edit-profile" style="display: block; text-align: center; margin: 0 auto;">Edit
                            Profile</a>

                        <a href="#" id="logout" style="display: block; text-align: center; margin: 0 auto;">Logout</a>

                    </div>
                </div>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="user_page.php?page=notifications" class="menu-item"><i class="fas fa-bell"></i>
                            Notifications</a></li>
                    <li><a href="user_page.php?page=dashboard" class="menu-item"><i class="fas fa-tachometer-alt"></i>
                            Dashboard</a></li>
                    <li><a href="user_page.php?page=submit-ticket" class="menu-item"><i class="fas fa-ticket-alt"></i>
                            Submit Ticket</a></li>
                    <li><a href="user_page.php?page=my-tickets" class="menu-item"><i class="fas fa-clipboard-list"></i>
                            My Tickets</a></li>
                    <li><a href="user_page.php?page=Rating" class="menu-item"><i class="fas fa-comments"></i> Rating</a></li>
                    <li><a href="user_page.php?page=calendar" class="menu-item"><i class="fas fa-calendar-alt"></i>
                            Calendar</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="content">
            <?php
            $page = $_GET['page'] ?? 'dashboard';
            $allowed_pages = ['notifications', 'dashboard', 'submit-ticket', 'my-tickets', 'Rating', 'calendar'];
            if (in_array($page, $allowed_pages)) {
                if ($page === 'Rating') {
                    if (file_exists("$page.php")) {
                        include "$page.php";
                    } else {
                        echo "<h1>Rating Page Not Found</h1>";
                        echo "<p>The requested page could not be found.</p>";
                    }
                } else {
                    include "$page.php";
                }
            } else {
                echo "<h1>Welcome to the User Dashboard</h1>";
                echo "<p>Select an option from the menu to get started.</p>";
            }

            
            ?>
        </main>
    </div>
    <script src="user.js"></script>

</body>

</html>