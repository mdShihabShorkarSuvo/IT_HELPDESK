<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        /* Reset some default styles */
        body, h1, p, table {
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
            max-width: 600px;
            padding: 40px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
 
        /* Styling for the main heading */
        h1 {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            margin-bottom: 20px;
        }
 
        /* Styling for the logo and company name */
        .logo-container {
            margin-bottom: 30px;
        }
 
        .logo-container img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
 
        .logo-container h2 {
            font-size: 24px;
            color: #333;
            margin-top: 0;
        }
 
        /* Styling for the contact info table */
        .contact-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            color: #333;
            text-align: left;
        }
 
        /* Table header styling */
        .contact-table th {
            background: #007bff;
            color: #fff;
            padding: 15px;
            border-bottom: 2px solid #ddd;
            text-align: left;
        }
 
        /* Table cell styling */
        .contact-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background: #f9f9f9;
            color: #333;
        }
 
        /* Alternating row background color */
        .contact-table tr:nth-child(even) {
            background: #f1f1f1;
        }
 
        /* Styling for the back button */
        .btn {
            display: inline-block;
            background: #ff4d4d;  /* Red background */
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
            background: #cc0000;  /* Darker red on hover */
        }
 
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }
 
            h1 {
                font-size: 28px;
            }
 
            .btn {
                font-size: 14px;
                padding: 8px 18px;
            }
 
            .contact-table th, .contact-table td {
                font-size: 16px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
 
    <div class="container">
        <!-- Logo and Company Name Section -->
        <div class="logo-container">
            <img src="images/logo.png" alt="Company Logo">
            <h1>SMART IT HELPDESK AND SUPPORTING SYSTEM</h1>
        </div>
 
        <h2>Contact Information</h2>
        <table class="contact-table">
            <tr>
                <th>Phone</th>
                <td>01734898719</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>support@smartithelpdesk.com</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>Kuratoli, Kuril Bissoroad , Dhaka </td>
            </tr>
        </table>
 
        <a href="index.php" class="btn">Go Back</a>
    </div>
 
</body>
</html>