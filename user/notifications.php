<?php
// Assuming the logged-in user's user_id is stored in a session or passed through a secure method.
$user_id = $_SESSION['user_id']; // Replace with actual method to get the logged-in user's ID

// Fetch notifications for the user based on user_id, including the "assigned by" name for updates
$sql = "SELECT t.ticket_id, t.title, t.created_at, t.updated_at, t.assigned_to, u.name AS assigned_by_name
        FROM tickets t
        LEFT JOIN users u ON t.assigned_to = u.user_id
        WHERE t.user_id = ?
        ORDER BY t.updated_at DESC";

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
echo "<div class='notification-heading'>Notifications</div>";

echo "<div class='notification-container'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if the ticket was just created or updated
        $message = "";
        if ($row['created_at'] == $row['updated_at']) {
            $message = "Ticket #" . $row['ticket_id'] . " has been created.";
        } else {
            // Check if the ticket is assigned, and display the appropriate message
            if (empty($row['assigned_by_name'])) {
                $assigned_by_message = "Assigned to: Not assigned yet";
            } else {
                $assigned_by_message = "Assigned to: " . $row['assigned_by_name'];
            }

            $message = "Ticket #" . $row['ticket_id'] . " has been updated. " . $assigned_by_message;
        }

        echo "<div class='notification' id='notification_" . $row['ticket_id'] . "'>";
        echo "<div class='notification-header'>" . $row['title'] . "</div>";
        echo "<div class='notification-message'>" . $message . "</div>";
        echo "<div class='notification-time-container'>";
        echo "<div class='notification-time'>Created: " . $row['created_at'] . "</div>";
        echo "<div class='notification-time'>Updated: " . $row['updated_at'] . "</div>";
        echo "</div>";
        echo "<button class='hide-btn' onclick='hideNotification(" . $row['ticket_id'] . ")'>Mark as Read</button>";
        echo "</div><hr>";
    }
} else {
    // Custom notification message when no tickets are found
    echo "<div class='notification'>";
    echo "<div class='notification-header'>No Notifications</div>";
    echo "<div class='notification-message'>You have no new tickets or updates at the moment.</div>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>

<!-- Link to External CSS -->
<link rel="stylesheet" href="css/notifications.css">

<!-- Link to External JavaScript -->
<script src="notifications.js"></script>
