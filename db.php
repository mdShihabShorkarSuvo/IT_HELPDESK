<?php
// Database connection details
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password for XAMPP
$dbname = "smart_it_helpdesk"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
