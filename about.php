<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us - Smart IT Helpdesk</title>
<style>
        /* Reset some default styles */
        body, h1, p {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
 
        /* Enlarged container with logo and name */
        .container {
            max-width: 1000px;
            padding: 60px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            text-align: center;
        }
 
        /* Styling for company logo and name */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
 
        .header img {
            max-width: 80px;
            margin-right: 15px;
        }
 
        .header h2 {
            font-size: 32px;
            color: #333;
            font-weight: bold;
        }
 
        /* Styling for the main heading */
        h1 {
            font-size: 42px;
            font-weight: bold;  
            color: #000;  
            line-height: 1.5;
            text-transform: uppercase;
            margin-bottom: 30px;
            word-wrap: break-word;
        }
 
        /* Ensuring each line of text appears evenly spaced */
        h1 span {
            display: block;
        }
 
        /* Styling for the paragraph */
        p {
            font-size: 22px;
            color: #333;
            margin-top: 20px;
        }
 
        /* Styling for the smaller red back button */
        .btn {
            display: inline-block;
            background: #ff0000; /* Red color */
            color: #fff;
            padding: 12px 30px;  /* Smaller size */
            font-size: 18px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 30px;
            transition: 0.3s ease-in-out;
            border: none;
            cursor: pointer;
        }
 
        /* Button hover effect */
        .btn:hover {
            background: #cc0000;
        }
 
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 30px;
            }
 
            h1 {
                font-size: 32px;
            }
 
            .header h2 {
                font-size: 24px;
            }
 
            .btn {
                font-size: 16px;
                padding: 10px 25px;
            }
        }
</style>
</head>
<body>
 
    <div class="container">
<!-- Logo and company name -->
<div class="header">
<img src="images/logo.png" alt="Company Logo">
<h2>Smart IT Helpdesk</h2>
</div>
 
        <h1>
<span>Our company provides all types of IT solutions,</span>
<span>offering services from hardware to software support.</span>
</h1>
<p>We ensure high-quality IT support to businesses and individuals globally.</p>
 
        <!-- Smaller red back button -->
<a href="index.php" class="btn">Go Back</a>
</div>
 
</body>
</html>