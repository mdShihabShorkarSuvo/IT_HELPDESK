<?php

// Include database connection
include '../db.php';

// Check if the user is logged in and is an IT staff (assuming role is 'it_staff')
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'it_staff') {
    header("Location: ../login.php");  // Redirect to login page if not logged in or not IT staff
    exit();
}

// Query to get the tickets, ratings, reviews, and assigned user info
$sql = "SELECT 
            tickets.ticket_id,
            tickets.title,
            tickets.user_id AS creator_id,
            creator.name AS creator_name,
            ticket_ratings.rating,
            ticket_ratings.review,
            tickets.assigned_to
        FROM 
            tickets
        JOIN 
            ticket_ratings ON tickets.ticket_id = ticket_ratings.ticket_id
        JOIN 
            users AS creator ON tickets.user_id = creator.user_id
        WHERE
            tickets.assigned_to = ?"; // Only show tickets assigned to the logged-in IT staff user

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Bind the current logged-in IT staff's user_id
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Staff - Ticket Ratings</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Assigned Ticket Ratings</h1>

<?php
if ($result->num_rows > 0) {
    // Open the table and header row
    echo "<table>";
    echo "<tr>";
    echo "<th>Ticket ID</th>";
    echo "<th>Title</th>";
    echo "<th>User ID</th>";
    echo "<th>User Name</th>";
    echo "<th>Rating</th>";
    echo "<th>Feedback</th>";
    echo "</tr>";

    // Display the tickets and their corresponding ratings and reviews
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ticket_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['creator_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['creator_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
        echo "<td>" . htmlspecialchars($row['review']) . "</td>";
        echo "</tr>";
    }

    // Close the table
    echo "</table>";
} else {
    echo "<p>No ratings available for your assigned tickets.</p>";
}
?>

</body>
</html>
