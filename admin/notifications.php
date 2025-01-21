<?php
// include 'db.php'; // Assuming the database connection is established

// Assuming the logged-in user's user_id is stored in a session or is passed through a secure method.
$user_id = $_SESSION['user_id']; // Replace with actual method to get the logged-in user's ID

// Fetch all tickets with the creator's name and assigned user's name
$sql = "SELECT t.ticket_id, t.title, t.created_at, t.updated_at, t.assigned_to, u.name AS creator_name, ua.name AS assigned_name 
        FROM tickets t 
        LEFT JOIN users u ON t.user_id = u.user_id  -- Join tickets table with users table to get creator's name
        LEFT JOIN users ua ON t.assigned_to = ua.user_id  -- Join users table again to get assigned user's name
        ORDER BY t.updated_at DESC";

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($sql);

// Execute the query
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
            $message = "A new ticket (#" . $row['ticket_id'] . ") has been created: " . $row['title'] . ".";
        } else {
            $message = "Ticket (#" . $row['ticket_id'] . ") has been updated: " . $row['title'] . ".";
        }

        // Check if assigned user exists, otherwise show "Not assigned yet"
        $assigned_message = ($row['assigned_name']) ? "Assigned to: " . $row['assigned_name'] : "Not assigned yet";

        // Message for admin showing all tickets with the creator's name and assigned user
        $creator_message = "Created by: " . $row['creator_name'];
        echo "<div class='notification' id='notification_" . $row['ticket_id'] . "'>";
        echo "<div class='notification-header'>" . $row['title'] . "</div>";
        echo "<div class='notification-message'>" . $message . "</div>";
        echo "<div class='notification-assigned'>" . $assigned_message . "</div>"; // Assigned user's name display or "Not assigned yet"
        echo "<div class='notification-creator'>" . $creator_message . "</div>"; // Creator's name display
        echo "<div class='notification-time'>Created on: " . $row['created_at'] . "</div>"; // Show creation time after the assigned message
        echo "<div class='notification-time'>Last updated: " . $row['updated_at'] . "</div>"; // Show update time after the creation time
        echo "<button class='hide-btn' onclick='hideNotification(" . $row['ticket_id'] . ")'>Mark as Read</button>";
        echo "</div><hr>";
    }
} else {
    // Custom notification message when no tickets are found
    echo "<div class='notification'>";
    echo "<div class='notification-header'>No Tickets</div>";
    echo "<div class='notification-message'>There are no tickets available at the moment.</div>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>

<!-- Link to External CSS -->
<link rel="stylesheet" href="css/notifications.css">

<!-- Link to External JavaScript -->
<script src="notifications.js"></script>
