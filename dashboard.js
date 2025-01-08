// Profile dropdown toggle
document.getElementById('profile-img').addEventListener('click', function() {
    const dropdown = document.querySelector('.profile-dropdown');
    dropdown.classList.toggle('show'); // Toggle the visibility of the dropdown
});

// Close dropdown if clicked outside
document.addEventListener('click', function(event) {
    const profileDropdown = document.querySelector('.profile-dropdown');
    const profileImage = document.getElementById('profile-img');
    if (!profileDropdown.contains(event.target) && event.target !== profileImage) {
        profileDropdown.classList.remove('show'); // Hide dropdown if clicked outside
    }
});

// Handle menu item clicks and set active state
document.querySelectorAll('.menu ul li a').forEach(menuItem => {
    menuItem.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior

        // Remove the active class from all menu items
        document.querySelectorAll('.menu ul li a').forEach(item => item.classList.remove('active'));
        // Add the active class to the clicked item
        this.classList.add('active');

        // Fetch the content for the selected page dynamically
        const page = this.getAttribute('data-page');
        
        // If the 'calendar' item is clicked, load the Google Calendar
        if (page === 'calendar') {
            loadGoogleCalendar();
        } else {
            // For other pages, dynamically change the content (this could be AJAX fetch, etc.)
            document.getElementById('content').innerHTML = `<h1>${page} content</h1>`;
        }
    });
});

// Function to embed Google Calendar
// Function to embed Google Calendar
function loadGoogleCalendar() {
    const contentArea = document.getElementById('content');
    
    // Check if the Google Calendar iframe is already embedded
    if (!contentArea.querySelector('iframe')) {
        // Create the iframe to embed Google Calendar
        const iframe = document.createElement('iframe');
        iframe.src = 'https://calendar.google.com/calendar/embed?src=en.bd%23holiday%40group.v.calendar.google.com&ctz=Asia%2FDhaka'; // Use your actual calendar ID here
        iframe.style.width = '100%';
        iframe.style.height = '600px';
        iframe.style.border = '0';
        iframe.setAttribute('frameborder', '0');
        iframe.setAttribute('scrolling', 'no');

        // Clear the content and append the iframe
        contentArea.innerHTML = '';
        contentArea.appendChild(iframe);
    } else {
        // If the iframe is already embedded, remove it
        contentArea.innerHTML = '';
    }
}


