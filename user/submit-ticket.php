<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Ticket</title>
    <link rel="stylesheet" href="form.css">
    <style>
        .ticket-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .ticket-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        .ticket-body {
            padding: 20px;
        }
        .ticket-body .form-group {
            margin-bottom: 15px;
        }
        .ticket-body label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .ticket-body input, .ticket-body select, .ticket-body textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .ticket-body .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .ticket-body .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">Submit Ticket</div>
        <div class="ticket-body">
            <form action="process.php" method="post" class="form">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="id">ID:</label>
                    <input type="text" id="id" name="student_id" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="Problem_categories">Problem Categories:</label>
                    <select id="Problem_categories" name="Problem_categories" required>
                        <option value="B1">Hardware</option>
                        <option value="B2">Software</option>
                        <option value="B3">Network Problems</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="token">Token Number:</label>
                    <input type="number" id="token" name="token" min="0">
                </div>

                <div class="form-group">
                    <label for="description">Problem Description:</label>
                    <textarea name="description" id="description" rows="5" cols="30" required></textarea>
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Submit" class="btn">
                </div>
            </form>
        </div>
    </div>
</body>
</html>