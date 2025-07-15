document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('routeSearchForm');
    const routesContainer = document.getElementById('routesContainer');

    if (form) {
        form.addEventListener('submit', function (e) {
            const from = document.getElementById('routeFrom').value.trim();
            const to = document.getElementById('routeTo').value.trim();

            if (!from && !to) {
                e.preventDefault();
                alert("Please select at least one city to search routes.");
            } else {
                if (routesContainer) {
                    routesContainer.innerHTML = '<p style="padding: 20px;">Loading filtered routes...</p>';
                }
                // The form will submit normally here and reload with filtered routes
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('book-now-btn')) {
            e.preventDefault();

            const routeId = e.target.getAttribute('data-route-id');
            if (routeId) {
                // Redirect to book.php with route_id
                window.location.href = `book.php?route_id=${routeId}`;
            } else {
                alert('Route ID not found.');
            }
        }
    });
});
