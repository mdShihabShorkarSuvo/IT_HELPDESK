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

// Handle menu item clicks and set the active state
document.querySelectorAll('.menu ul li a').forEach(menuItem => {
    menuItem.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default link behavior

        // Remove active class from all menu items
        document.querySelectorAll('.menu ul li a').forEach(item => item.classList.remove('active'));
        this.classList.add('active'); // Add active class to the clicked item

        // Get the page name from the data-page attribute
        const page = this.getAttribute('data-page');

        // Dynamically fetch and load the content
        if (page) {
            loadPageContent(page);
        }
    });
});

// Function to load page content dynamically
function loadPageContent(page) {
    fetch(`./${page}.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error: ${response.statusText}`);
            }
            return response.text();
        })
        .then(data => {
            const contentArea = document.getElementById('content');
            contentArea.innerHTML = data; // Load the fetched content
        })
        .catch(error => {
            console.error('Error loading page:', error);
            document.getElementById('content').innerHTML = `<p class="error">Failed to load the page. Please try again later.</p>`;
        });
}