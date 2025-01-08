document.getElementById('signupForm').onsubmit = function(event) {
    let valid = true;

    // Reset previous error messages
    document.querySelectorAll('.error').forEach(el => el.innerText = '');

    let name = document.getElementById('name').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value;
    let cpassword = document.getElementById('cpassword').value;
    let phone = document.getElementById('phone').value;
    let birth_date = document.getElementById('birth_date').value;
    let today = new Date().toISOString().split('T')[0];

    // Name Validation
    if (name === "") {
        document.getElementById('name-error').innerText = "Full name is required.";
        valid = false;
    }

    // Email Validation
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        document.getElementById('email-error').innerText = "Invalid email format.";
        valid = false;
    }

    // Password Validation
    if (password.length < 6) {
        document.getElementById('password-error').innerText = "Password must be at least 6 characters.";
        valid = false;
    }

    // Confirm Password Validation
    if (password !== cpassword) {
        document.getElementById('cpassword-error').innerText = "Passwords do not match.";
        valid = false;
    }

    // Phone Validation (10-15 digits)
    let phonePattern = /^[0-9]{10,15}$/;
    if (!phonePattern.test(phone)) {
        document.getElementById('phone-error').innerText = "Phone number must be 10 to 15 digits.";
        valid = false;
    }

    // Birth Date Validation
    if (birth_date > today) {
        document.getElementById('birth-error').innerText = "Birth date cannot be in the future.";
        valid = false;
    }

    // Form Submission if Validation Passes
    if (!valid) {
        event.preventDefault();  // Stop submission if invalid
    } else {
        alert("Form submitted successfully!");
        // Form will proceed to process.php
    }
};
