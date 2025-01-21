<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support Dashboard</title>
    <link rel="stylesheet" href="css/submit-ticket.css"> <!-- Link to external CSS file -->
</head>
<body>
<div id="message"></div> 

    <h1>IT Support Dashboard</h1>

    <!-- The form is now always visible -->
    <div class="form-container" id="ticketForm">
        <form action="submit-process.php" method="post" enctype="multipart/form-data">
            
            <!-- Room No -->
            <div class="form-group">
                <label for="roomNo">Room No:</label>
                <input type="text" id="roomNo" name="roomNo" placeholder="Enter your room number" required>
            </div>

            <!-- PC No -->
            <div class="form-group">
                <label for="pcNo">PC No:</label>
                <input type="text" id="pcNo" name="pcNo" placeholder="Enter your PC number" required>
            </div>

            <!-- Category -->
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="">Choose one</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                    <option value="Network">Network Problems</option>
                </select>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter the title of your request" required>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Request Details:</label>
                <textarea id="description" name="description" rows="5" placeholder="How can we help?" required></textarea>
            </div>

            <!-- Attachment -->
            <div class="form-group">
                <label for="attachment">Attachment:</label>
                <input type="file" id="attachment" name="attachment">
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<p style='color: green;'>Ticket submitted successfully!</p>";
}
?>
