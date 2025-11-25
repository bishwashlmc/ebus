// Get seat elements
const seats = document.querySelectorAll('.seat:not(.booked)');

const selectedSeatDisplay = document.getElementById('selectedSeat');
const totalPriceDisplay = document.getElementById('totalPrice');
const payBtn = document.getElementById('payBtn');

// MAKE selectedSeats GLOBAL
window.selectedSeats = [];

// Read price correctly
const pricePerSeat = parseFloat(document.getElementById('price_per_seat').value);

// Update price UI
function updateTotalPrice() {
    const total = window.selectedSeats.length * pricePerSeat;
    totalPriceDisplay.textContent = `Total Price: Rs. ${total}`;
}

// Update seat text + enable/disable pay button
function updateSeatDisplay() {
    if (window.selectedSeats.length > 0) {
        selectedSeatDisplay.textContent = `You selected: ${window.selectedSeats.join(', ')}`;
        payBtn.disabled = false;
        
        // Update hidden input for form submission
        document.getElementById('selectedSeatsJSON').value = JSON.stringify(window.selectedSeats);
    } else {
        selectedSeatDisplay.textContent = '';
        payBtn.disabled = true;
    }
    updateTotalPrice();
}

// Seat click handler
seats.forEach(seat => {
    seat.addEventListener('click', () => {

        if (!isLoggedIn) {
            alert("Please log in to book seats.");
            window.location.href = "login.php";
            return;
        }

        const seatNum = seat.dataset.seat;

        if (seat.classList.contains('selected')) {
            seat.classList.remove('selected');
            window.selectedSeats = window.selectedSeats.filter(s => s !== seatNum);
        } else {
            if (window.selectedSeats.length >= 4) {
                alert("You can book a maximum of 4 seats.");
                return;
            }
            seat.classList.add('selected');
            window.selectedSeats.push(seatNum);
        }

        updateSeatDisplay();
    });
});

// Start with disabled button
payBtn.disabled = true;

// Handle form submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log("📝 Form submitted");
    
    // Double check login
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert("⚠️ Please login first to book tickets!");
        window.location.href = "login.php";
        return false;
    }
    
    // Validate seats
    if (window.selectedSeats.length === 0) {
        alert("⚠️ Please select at least one seat.");
        return false;
    }
    
    console.log("✅ Submitting booking for seats:", window.selectedSeats);
    console.log("💰 Total amount:", window.selectedSeats.length * pricePerSeat);
    
    // Submit the form
    this.submit();
});