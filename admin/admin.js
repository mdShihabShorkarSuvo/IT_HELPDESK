// Profile dropdown toggle
document.getElementById('profile-img').addEventListener('click', function () {
    const dropdown = document.querySelector('.profile-dropdown');
    dropdown.classList.toggle('show'); // Toggle the visibility of the dropdown
});

// Close profile dropdown when clicked outside
document.addEventListener('click', function (event) {
    const profileDropdown = document.querySelector('.profile-dropdown');
    const profileImage = document.getElementById('profile-img');
    if (profileDropdown && !profileDropdown.contains(event.target) && event.target !== profileImage) {
        profileDropdown.classList.remove('show'); // Hide the dropdown
    }
});

// Handle "Edit Profile" click
document.getElementById('edit-profile').addEventListener('click', function () {
    window.location.href = 'edit-profile.php'; // Redirect to the edit profile page
});

// Handle "Logout" click
document.getElementById('logout').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the link's default action

    // Ask for confirmation before logging out
    const confirmation = confirm('Are you sure you want to log out?');
    if (confirmation) {
        window.location.href = 'logout.php'; // Redirect to logout.php to end the session
    }
});
