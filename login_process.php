<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Check if fields are not empty
    if (empty($email) || empty($password) || empty($role)) {
        die("All fields are required.");
    }

    // Prepare SQL to fetch user details
    $sql = "SELECT email, password, role FROM users WHERE email = ? AND role = ?"; // SQL with parameters
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $role); // Bind variables to the prepared statement as parameters
    $stmt->execute(); // Execute the prepared statement
    $result = $stmt->get_result(); 

    // Check if a user with the given email and role exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the entered password with the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Set cookies for email and role (expires in 7 days)
            setcookie("email", $user['email'], time() + (86400 * 7)); // Cookie valid for 7 days
            setcookie("role", $user['role'], time() + (86400 * 7)); // Cookie valid for 7 days

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/admin_page.php");
            } elseif ($user['role'] === 'it_staff') {
                header("Location: staff/it_staff.php");
            } elseif ($user['role'] === 'user') {
                header("Location: user/user_page.php");
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with the given email and role.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
