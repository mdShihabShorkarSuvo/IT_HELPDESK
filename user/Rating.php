<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the role of 'user'
if ($_SESSION['role'] !== 'user') {
    header("Location: ../login.php");  // Redirect to login page if not authorized
    exit();
}

include '../db.php'; // Database connection

$user_email = $_SESSION['email'];

// Query to fetch resolved tickets for the logged-in user
$sql = "SELECT ticket_id, title FROM tickets WHERE user_id = ? AND status = 'Resolved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission and set a session flag
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert the rating into the ticket_ratings table
    $insert_sql = "INSERT INTO ticket_ratings (ticket_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiis", $ticket_id, $_SESSION['user_id'], $rating, $review);

    if ($insert_stmt->execute()) {
        // Set session flag to mark the ticket as rated
        $_SESSION['rated_tickets'][$ticket_id] = true; // Store the rated ticket in session
        echo "<script>hideForm($ticket_id);</script>";
    } else {
        echo "<script>alert('Failed to submit rating. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolved Tickets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
// JavaScript function to hide the form after submission
function hideForm(ticket_id) {
    var formElement = document.getElementById('ticket-form-' + ticket_id);
    formElement.style.display = 'none'; // Hide the form but don't delete any database entry
}
</script>
</head>
<body>
    <h1>All Resolved Tickets</h1>

    <div id="message" style="display: none; color: green; font-weight: bold;"></div>

    <?php
    // Loop through resolved tickets and display rating form
    if ($result->num_rows > 0) {
        while ($ticket = $result->fetch_assoc()) {
            $ticket_id = $ticket['ticket_id'];
            // Check if the ticket has already been rated by the user
            if (isset($_SESSION['rated_tickets'][$ticket_id]) && $_SESSION['rated_tickets'][$ticket_id]) {
                continue; // Skip displaying the form if the ticket has been rated
            }
    ?>
        <form method="POST" action="" class="rating-form" id="ticket-form-<?= $ticket['ticket_id'] ?>">
            <!-- Hidden input to send ticket ID -->
            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['ticket_id']) ?>">

            <!-- Ticket Title -->
            <label for="rating"><?= htmlspecialchars($ticket['title']) ?></label>

            <!-- Star Rating System -->
            <div class="star-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="star<?= $i . '-' . $ticket['ticket_id'] ?>" name="rating" value="<?= $i ?>" required>
                    <label for="star<?= $i . '-' . $ticket['ticket_id'] ?>" title="<?= $i ?> stars">&#9733;</label>
                <?php endfor; ?>
            </div>

            <!-- Review Textarea -->
            <textarea name="review" placeholder="Write your review here" rows="4" required></textarea>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">Submit Rating</button>
        </form>
    <?php
        }
    } else {
        echo "<p>No resolved tickets available for rating.</p>";
    }
    ?>
</body>

<style>
/* General Styling */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 20px;
}

/* Rating Form Container */
.rating-form {
    margin: 20px 0;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

/* Star Rating System */
.star-rating {
    direction: rtl;
    display: inline-block;
    margin: 10px 0;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    font-size: 2em;
    color: #ddd;
    cursor: pointer;
}

.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #f5b301;
}

/* Textarea Styling */
textarea {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1em;
    resize: vertical;
}

/* Submit Button */
button {
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1em;
    margin-top: 10px;
}

button:hover {
    background-color: #0056b3;
}

/* Message Styling */
#message {
    margin: 10px 0;
    padding: 10px;
    background-color: #d4edda;
    color: #155724;
    border-radius: 5px;
}

/* Hide Form if Rated */
#ticket-form-<?= $ticket['ticket_id'] ?> {
    display: block;
    transition: opacity 0.5s ease-out;
}

.hidden {
    display: none;
}
</style>

</html>
