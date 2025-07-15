
document.getElementById('currentYear').textContent = new Date().getFullYear();


const routes = [
    {
        id: 1,
        from: 'Kathmandu',
        to: 'Pokhara',
        image: 'https://images.unsplash.com/photo-1581779166680-db5740d0d421?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '6-7 hours',
        price: 1200
    },
    {
        id: 2,
        from: 'Kathmandu',
        to: 'Chitwan',
        image: 'https://images.unsplash.com/photo-1566438480900-0609be27a4be?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '5-6 hours',
        price: 900
    },
    {
        id: 3,
        from: 'Pokhara',
        to: 'Lumbini',
        image: 'https://images.unsplash.com/photo-1513415277900-a62401e19be4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '4-5 hours',
        price: 800
    },
    {
        id: 4,
        from: 'Kathmandu',
        to: 'Dharan',
        image: 'https://images.unsplash.com/photo-1533575770077-052fa2c609fc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '8-9 hours',
        price: 1500
    },
    {
        id: 5,
        from: 'Pokhara',
        to: 'Jomsom',
        image: 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '7-8 hours',
        price: 1800
    },
    {
        id: 6,
        from: 'Kathmandu',
        to: 'Biratnagar',
        image: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        duration: '10-12 hours',
        price: 2000
    }
];

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


function loadTestimonials() {
    const testimonialsContainer = document.getElementById('testimonialsContainer');
    testimonialsContainer.innerHTML = '';
    
    testimonials.forEach(testimonial => {
        const testimonialCard = document.createElement('div');
        testimonialCard.className = 'testimonial-card';
        testimonialCard.innerHTML = `
            <div class="testimonial-text">
                <p>${testimonial.text}</p>
            </div>
            <div class="testimonial-author">
                <div class="author-avatar">
                    <img src="${testimonial.avatar}" alt="${testimonial.name}" width="50" height="50">
                </div>
                <div class="author-info">
                    <h4>${testimonial.name}</h4>
                    <p>${testimonial.role}</p>
                </div>
            </div>
        `;
        testimonialsContainer.appendChild(testimonialCard);
    });
}


document.getElementById('busSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;
    const date = document.getElementById('date').value;
    
   
    console.log('Searching buses:', { from, to, date });
    alert(`Searching buses from ${from} to ${to} on ${date}`);
});

document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('newsletterEmail').value;
    
  
    console.log('Subscribed email:', email);
    alert(`Thank you for subscribing with ${email}!`);
    this.reset();
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


document.addEventListener('DOMContentLoaded', function() {
    loadRoutes();
    loadTestimonials();
    
  
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('date').valueAsDate = tomorrow;
});