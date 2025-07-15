


document.addEventListener('DOMContentLoaded', function() {
    loadRoutes();
    loadTestimonials();
    

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('date').valueAsDate = tomorrow;
});

function loadRoutes() {
    const routesContainer = document.getElementById('routesContainer');
    routesContainer.innerHTML = '';
    
    routes.forEach(route => {
        const routeCard = document.createElement('div');
        routeCard.className = 'route-card';
        routeCard.innerHTML = `
            <div class="route-image" style="background-image: url('${route.image}')"></div>
            <div class="route-info">
                <h3>${route.from} to ${route.to}</h3>
                <p>Travel time: ${route.duration}</p>
                <div class="route-price">Rs. ${route.price}</div>
                <a href="#" class="book-now" data-route-id="${route.id}">Book Now</a>
            </div>
        `;
        routesContainer.appendChild(routeCard);
    });
}



document.getElementById('busSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;
    const date = document.getElementById('date').value;
    
    alert(`Searching buses from ${from} to ${to} on ${date}`);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('book-now')) {
        e.preventDefault();
        const routeId = e.target.getAttribute('data-route-id');
        const route = routes.find(r => r.id == routeId);
        
        if (route) {
            alert(`Booking route from ${route.from} to ${route.to} for Rs. ${route.price}`);
        }
    }
});