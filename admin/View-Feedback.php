<?php

// Include database connection
include '../db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");  // Redirect to login page if not logged in or not admin
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
            tickets.assigned_to AS assigned_user_id,
            assigned.name AS assigned_user_name
        FROM 
            tickets
        JOIN 
            ticket_ratings ON tickets.ticket_id = ticket_ratings.ticket_id
        JOIN 
            users AS creator ON tickets.user_id = creator.user_id
        JOIN 
            users AS assigned ON tickets.assigned_to = assigned.user_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ticket Ratings</title>
    <link rel="stylesheet" href="css/ticket-ratings.css">
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

<h1>All Ticket Ratings</h1>

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
    echo "<th>Assigned It Staff ID</th>";
    echo "<th>Assigned It Staff Name</th>";
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
        echo "<td>" . htmlspecialchars($row['assigned_user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['assigned_user_name']) . "</td>";
        echo "</tr>";
    }

    // Close the table
    echo "</table>";
} else {
    echo "<p>No ratings available.</p>";
}
?>

</body>
</html>
