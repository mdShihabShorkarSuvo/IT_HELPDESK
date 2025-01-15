// notifications.js

// When the page is loaded, check localStorage to hide previously hidden notifications
document.addEventListener('DOMContentLoaded', function () {
    // Get hidden notifications from localStorage (if any)
    var hiddenNotifications = JSON.parse(localStorage.getItem('hiddenNotifications')) || [];
    
    // Loop through the hidden notifications and hide them
    hiddenNotifications.forEach(function(ticketId) {
        var notification = document.getElementById('notification_' + ticketId);
        if (notification) {
            notification.classList.add('hide');  // Add 'hide' class to hide the notification
        }
    });
});

// Function to hide notification and store the ticket ID in localStorage
function hideNotification(ticketId) {
    // Hide the notification visually by adding the 'hide' class
    var notification = document.getElementById('notification_' + ticketId);
    if (notification) {
        notification.classList.add('hide');
    }
    
    // Get the list of hidden notifications from localStorage
    var hiddenNotifications = JSON.parse(localStorage.getItem('hiddenNotifications')) || [];
    
    // Add the current ticketId to the list of hidden notifications (if it's not already there)
    if (!hiddenNotifications.includes(ticketId)) {
        hiddenNotifications.push(ticketId);
    }

    // Store the updated list back into localStorage
    localStorage.setItem('hiddenNotifications', JSON.stringify(hiddenNotifications));
}
