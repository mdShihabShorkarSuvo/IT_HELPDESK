<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="login.js"></script>
    
</head>
<body>
    <div class="login-container">
        <a href="index.php" class="close-btn" title="Close">&times;</a>
        <h2>LOGIN</h2>
        <form action="login_process.php" method="POST">
            <div class="input-field">
                <i class="fas fa-envelope icon"></i>
                <input type="text" name="email" placeholder="Email" required>
            </div>
            <div class="input-field">
                <i class="fas fa-lock icon"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="custom-dropdown">
                <select name="role" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="user">User</option>
                    <option value="it_staff">IT Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <a href="#" class="forgot-pass-link">Forgot password?</a>
            <button type="submit">Log In</button>
        </form>
        <div class="signup-prompt">
            Don't have an account?
            <a href="signup.php"  class="signup-link"  id="signup-link">Signup</a>
        </div>
    </div>
</body>
</html>
