// Only target real seat elements, not the legend
const seats = document.querySelectorAll('.seat:not(.legend-seat)');

const selectedSeatDisplay = document.getElementById('selectedSeat');
const totalPriceDisplay = document.getElementById('totalPrice');
const bookingForm = document.getElementById('bookingForm');
const submitBtn = document.getElementById('submitBooking');

let selectedSeats = [];

const pricePerSeatInput = document.querySelector('input[name="price_per_seat"]');
const pricePerSeat = pricePerSeatInput ? parseFloat(pricePerSeatInput.value) : 0;

function updateTotalPrice() {
    const total = selectedSeats.length * pricePerSeat;
    totalPriceDisplay.textContent = `Total Price: Rs. ${total.toFixed(2)}`;
}

function updateSeatDisplay() {
    if (selectedSeats.length > 0) {
        selectedSeatDisplay.textContent = `You selected: ${selectedSeats.join(', ')}`;
        submitBtn.disabled = false;
    } else {
        selectedSeatDisplay.textContent = '';
        submitBtn.disabled = true;
    }
    updateTotalPrice();
}

seats.forEach(seat => {
    seat.addEventListener('click', () => {
        if (seat.classList.contains('booked')) return;

        const seatNum = seat.dataset.seat;

        if (!isLoggedIn) {
            alert("Please log in to book seats.");
            window.location.href = "login.php"; // Redirect to login page
            return;
        }

        if (seat.classList.contains('selected')) {
            seat.classList.remove('selected');
            selectedSeats = selectedSeats.filter(s => s !== seatNum);
        } else {
            if (selectedSeats.length >= 4) {
                alert("You can book a maximum of 4 seats.");
                return;
            }
            seat.classList.add('selected');
            selectedSeats.push(seatNum);
        }

        updateSeatDisplay();
    });
});

bookingForm.addEventListener('submit', function(e) {
    // Remove any previously added hidden seat inputs
    const oldInputs = this.querySelectorAll('input[name="selectedSeats[]"]');
    oldInputs.forEach(input => input.remove());

    // Add new hidden inputs
    selectedSeats.forEach(seat => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selectedSeats[]';
        input.value = seat;
        this.appendChild(input);
    });

    // Optional: include total price
    if (pricePerSeatInput) {
        const totalPriceInput = document.createElement('input');
        totalPriceInput.type = 'hidden';
        totalPriceInput.name = 'total_price';
        totalPriceInput.value = (pricePerSeat * selectedSeats.length).toFixed(2);
        this.appendChild(totalPriceInput);
    }
});
