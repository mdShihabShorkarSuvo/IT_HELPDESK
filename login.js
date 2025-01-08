function validateLoginForm() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const role = document.getElementById('role').value;

    // Basic validation
    if (!email || !password || !role) {
        alert('Please fill out all fields.');
        return false;
    }

    // Regular expression for email validation
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    return true;  // Proceed to submit the form
}
