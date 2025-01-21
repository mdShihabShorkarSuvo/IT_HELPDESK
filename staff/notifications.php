<?php
// include 'db.php'; // Assuming the database connection is established

// Assuming the logged-in user's user_id is stored in a session or is passed through a secure method.
$user_id = $_SESSION['user_id']; // Replace with actual method to get the logged-in user's ID

// Fetch notifications for the IT staff based on user_id (assigned_to foreign key) and include the creator's name from the users table
$sql = "SELECT t.ticket_id, t.title, t.created_at, t.updated_at, t.assigned_to, u.name AS creator_name 
        FROM tickets t 
        LEFT JOIN users u ON t.user_id = u.user_id 
        WHERE t.assigned_to = ? 
        ORDER BY t.updated_at DESC";

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
echo "<div class='notification-heading'>Notifications</div>";

echo "<div class='notification-container'>";

// Add the "Notification" heading at the top
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if the ticket was just created or updated
        $message = "";
        if ($row['created_at'] == $row['updated_at']) {
            $message = "A new ticket (#" . $row['ticket_id'] . ") has been assigned to you: " . $row['title'] . ".";
        } else {
            $message = "Ticket (#" . $row['ticket_id'] . ") has been updated and has been assigned to you: " . $row['title'] . ".";
        }

        // Show the creator's name in the notification
        $creator_name = $row['creator_name'] ? $row['creator_name'] : 'Unknown User';

        echo "<div class='notification' id='notification_" . $row['ticket_id'] . "'>";
        echo "<div class='notification-header'>" . $row['title'] . "</div>";
        echo "<div class='notification-message'>" . $message . "</div>";
        echo "<div class='notification-creator'>Created by: " . $creator_name . "</div>"; // Display the creator's name
        echo "<div class='notification-time'>Created on: " . $row['created_at'] . " | Last updated: " . $row['updated_at'] . "</div>";
        echo "<button class='hide-btn' onclick='hideNotification(" . $row['ticket_id'] . ")'>Mark as Read</button>";
        echo "</div><hr>";
    }
} else {
    // Custom notification message when no tickets are found
    echo "<div class='notification'>";
    echo "<div class='notification-header'>No New Tickets</div>";
    echo "<div class='notification-message'>You don't have any new tickets assigned at the moment. Stay tuned!</div>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>

<!-- Link to External CSS -->
<link rel="stylesheet" href="css/notifications.css">

<!-- Link to External JavaScript -->
<script src="notifications.js"></script>
