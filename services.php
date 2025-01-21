<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <style>
        /* Reset default styles */
        body, h1, p, ul {
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
       
        /* Centered container with a shadow effect */
        .container {
            max-width: 800px;
            padding: 50px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
 
        /* Styling for the logo and company name */
        .logo-container {
            margin-bottom: 30px;
        }
 
        .logo-container img {
            width: 100px;  /* Fixed logo size */
            height: auto;
            margin-bottom: 10px;
        }
 
        .logo-container h1 {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-top: 0;
            text-transform: uppercase;
        }
 
        /* Styling for the main heading */
        h2 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }
 
        /* Styling for the services list */
        ul {
            list-style-type: none;
            padding: 0;
        }
 
        ul li {
            font-size: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
 
        ul li:last-child {
            border-bottom: none;
        }
 
        /* Styling for the back button */
        .btn {
            display: inline-block;
            background: #ff4d4d;
            color: #fff;
            padding: 10px 20px;  /* Smaller size */
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
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
                padding: 20px;
            }
 
            .logo-container img {
                width: 80px;
            }
 
            .logo-container h1 {
                font-size: 24px;
            }
 
            h2 {
                font-size: 22px;
            }
 
            ul li {
                font-size: 18px;
            }
 
            .btn {
                font-size: 14px;
                padding: 8px 18px;
            }
        }
    </style>
</head>
<body>
 
    <div class="container">
        <!-- Logo and Company Name Section -->
        <div class="logo-container">
            <img src="images/logo.png" alt="Company Logo">
            <h1>SMART IT HELPDESK AND SUPPORT SYSTEM</h1>
        </div>
 
        <h2>Our IT Services</h2>
        <ul>
            <li>Hardware Support</li>
            <li>Software Installation & Support</li>
            <li>Network Solutions</li>
            <li>IT Consultation Services</li>
            <li>Cybersecurity Services</li>
            <li>Data Backup and Recovery</li>
            <li>Remote IT Support</li>
           
           
        </ul>
 
        <a href="index.php" class="btn">Go Back</a>
    </div>
 
</body>
</html>
 