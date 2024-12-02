document.addEventListener('DOMContentLoaded', function() {
    // Handle tab navigation
    const navLinks = document.querySelectorAll('nav a');
    const sections = document.querySelectorAll('main section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            sections.forEach(section => {
                section.style.display = section.id === targetId ? 'block' : 'none';
            });
        });
    });

    // Show only the first section by default
    sections.forEach((section, index) => {
        section.style.display = index === 0 ? 'block' : 'none';
    });

    // Handle edit and delete button clicks
    document.querySelectorAll('.edit, .delete, .view').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.classList.contains('edit') ? 'edit' : 
                           this.classList.contains('delete') ? 'delete' : 'view';
            const row = this.closest('tr');
            const id = row.querySelector('td:first-child').textContent;
            
            // In a real application, you'd send an AJAX request to the server
            // For now, we'll just log the action
            console.log(`${action} item with ID: ${id}`);
            
            if (action === 'delete') {
                if (confirm('Are you sure you want to delete this item?')) {
                    row.remove();
                }
            }
        });
    });
});