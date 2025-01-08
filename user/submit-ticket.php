<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support Dashboard</title>
    <style>
        /* Reset styles */
        body, html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f5f7;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            display: block;
            margin: 20px auto;
            background-color: #4e73df;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #375a7f;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            resize: none;
        }

        .btn-submit {
            background-color: #4e73df;
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #375a7f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>IT Support Dashboard</h1>

        <!-- The form is now always visible -->
        <div class="form-container" id="formContainer">
            <form action="process.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category" >Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Choose one</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Network">Network Problems</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" placeholder="Enter the title of your request" required>
                </div>

                <div class="form-group">
                    <label for="description">Request Details:</label>
                    <textarea id="description" name="description" rows="5" placeholder="How can we help?" required></textarea>
                </div>

                <div class="form-group">
                    <label for="attachment">Attachment:</label>
                    <input type="file" id="attachment" name="attachment">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
