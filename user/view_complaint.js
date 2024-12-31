$(document).ready(function() {
    // View complaint details
    $('.view-complaint').click(function() {
        var button = $(this);

        // Get data from button attributes
        var data = {
            title: button.data('title'),
            description: button.data('description'),
            location: button.data('location'),
            status: button.data('status'),
            createdAt: button.data('createdAt')
        };

        // Debugging - log the data
        console.log('Complaint Data:', data); 

        // Ensure data is not empty
        if (!data.title || !data.description || !data.location || !data.status || !data.createdAt) {
            console.error('Missing data for complaint');
            return;  // Exit if data is missing
        }

        // Update modal content
        $('#viewComplaintModal .complaint-title').text(data.title);
        $('#viewComplaintModal .complaint-description').text(data.description || 'No description provided');
        $('#viewComplaintModal .complaint-location').text(data.location);
        
        // Add status with styled badge
        var statusClass = 
            data.status === 'completed' ? 'success' :
            data.status === 'in_progress' ? 'primary' :
            data.status === 'pending' ? 'warning' : 'secondary';
        
        $('#viewComplaintModal .complaint-status').html(
            `<span class="badge badge-${statusClass}">${data.status.toUpperCase()}</span>`
        );
        
        $('#viewComplaintModal .complaint-date').text(data.createdAt);
    });
});
