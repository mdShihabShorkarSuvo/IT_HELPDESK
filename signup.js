// Handle form submission
document.getElementById('signupForm').onsubmit = function(event) {
    let valid = true; 

    // Reset previous error messages
    document.querySelectorAll('.error').forEach(el => el.innerText = ''); // Clear any previous error messages

    // Collect form values
    let name = document.getElementById('name').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value;
    let cpassword = document.getElementById('cpassword').value;
    let phone = document.getElementById('phone').value;
    let birth_date = document.getElementById('birth_date').value;
    let today = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format

    // Name Validation
    if (name === "") {
        document.getElementById('name-error').innerText = "Full name is required."; // Show error if name is empty
        valid = false; // Set valid to false to indicate failed validation
    }

    // Email Validation (simple pattern check)
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        document.getElementById('email-error').innerText = "Invalid email format."; // Show error if email is invalid
        valid = false; // Set valid to false
    }

    // Password Validation
    if (password.length < 8) {
        document.getElementById('password-error').innerText = "Password must be at least 8 characters."; // Error if password is too short
        valid = false; // Set valid to false
    }

    // Confirm Password Validation
    if (password !== cpassword) {
        document.getElementById('cpassword-error').innerText = "Passwords do not match."; // Error if passwords don't match
        valid = false; // Set valid to false
    }

    // Phone Validation (ensures 10 to 15 digits)
    let phonePattern = /^[0-9]{10,15}$/;
    if (!phonePattern.test(phone)) {
        document.getElementById('phone-error').innerText = "Phone number must be 10 to 15 digits."; // Error if phone number is not valid
        valid = false; // Set valid to false
    }

    // Birth Date Validation (ensures date is not in the future)
    if (birth_date > today) {
        document.getElementById('birth-error').innerText = "Birth date cannot be in the future."; // Error if birth date is in the future
        valid = false; // Set valid to false
    }

    // AJAX Email Validation (checks if email already exists in the database)
    if (valid) { 
        let xhr = new XMLHttpRequest(); // Create a new XMLHttpRequest object
        xhr.open('POST', 'check_email.php', true); // Open a POST request to 'check_email.php'
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Set the request content type

        // Handle the response from the server
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) { // Check if request is complete and successful
                let response = xhr.responseText.trim(); // Get the server response and trim any extra spaces
                let emailError = document.getElementById('email-error'); // Get the email error element
                let valid = true; // Flag for email validation

                if (response === 'exists') { // If the email is already registered
                    emailError.innerText = "Email is already registered."; // Show email already registered error message
                    emailError.style.color = 'red'; // Set error message color to red
                    valid = false; // Set valid to false
                } else {
                    emailError.innerText = ""; // Clear email error if the email is available
                }

                // Proceed with form submission if email is valid
                if (valid) {
                    document.getElementById('signupForm').submit(); // Submit the form if valid
                } else {
                    event.preventDefault(); // Stop form submission if email is invalid
                }
            }
        };

        // Send the email data to the server
        xhr.send('email=' + encodeURIComponent(email));

        // Prevent form submission until AJAX request completes
        event.preventDefault();
    } else {
        // If form validation fails, prevent form submission
        event.preventDefault();
    }
};
