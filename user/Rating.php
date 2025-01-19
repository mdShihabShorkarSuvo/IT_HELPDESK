<?php
// Display all resolved tickets with a rating form
echo "<h1>All Resolved Tickets</h1>";

// Query to fetch resolved tickets for the logged-in user
$sql = "SELECT ticket_id, title FROM tickets WHERE user_id = ? AND status = 'Resolved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert the rating into the ticket_ratings table
    $insert_sql = "INSERT INTO ticket_ratings (ticket_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiis", $ticket_id, $_SESSION['user_id'], $rating, $review);

    if ($insert_stmt->execute()) {
        // Send SMS notification after successful rating submission
        $message = "Thank you for your feedback! Your rating for ticket ID $ticket_id has been submitted successfully.";
        include 'send_sms.php'; // Include the SMS function from a separate file
        sendSMS($message);

        echo "<p>Rating submitted successfully!</p>";
    } else {
        echo "<p>Failed to submit rating. Please try again.</p>";
    }
}

// Loop through resolved tickets and display rating form
while ($ticket = $result->fetch_assoc()) {
    ?>
    <form method="POST" action="" class="rating-form">
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
        <button type="submit">Submit Rating</button>
    </form>
    <?php
}
?>

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
</style>
