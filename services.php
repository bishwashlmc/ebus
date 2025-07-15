<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Services | ebus Nepal</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    
    <section class="page-hero">
      <div class="container">
        <h1>Our Services</h1>
        <p>
          Discover the range of services we offer to make your journey
          comfortable and convenient
        </p>
      </div>
    </section>

    <section class="services-section" style="padding: 60px 0">
      <div class="container">
        <div class="section-title">
          <h2>What We Offer</h2>
          <p>
            We provide comprehensive bus transportation services tailored to
            meet your travel needs
          </p>
        </div>

        <div
          class="services-grid"
          style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
          "
        >
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-bus"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">
              Intercity Travel
            </h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              Comfortable and reliable bus services connecting major cities
              across Nepal with various departure times to suit your schedule.
            </p>
            <a href="routes.php" class="btn">View Routes</a>
          </div>
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-ticket-alt"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">Online Booking</h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              Easy and secure online ticket booking system with multiple payment
              options and instant confirmation.
            </p>
            <a href="routes.php" class="btn">Book Now</a>
          </div>
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-suitcase"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">Luggage Service</h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              Generous luggage allowance with secure storage and handling to
              ensure your belongings travel safely with you.
            </p>
            <a href="#" class="btn">Learn More</a>
          </div>
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-wifi"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">
              Onboard Amenities
            </h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              Enjoy complimentary WiFi, charging ports, comfortable seating, and
              onboard entertainment on select routes.
            </p>
            <a href="#" class="btn">View Options</a>
          </div>
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-users"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">Group Travel</h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              Special arrangements and discounts for group travel, school trips,
              and corporate outings.
            </p>
            <a href="contact.php" class="btn">Contact Us</a>
          </div>
          <div
            class="service-card"
            style="
              background-color: white;
              padding: 30px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
              text-align: center;
              transition: transform 0.3s;
            "
          >
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 20px">
              <i class="fas fa-headset"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50">
              Customer Support
            </h3>
            <p style="color: #7f8c8d; margin-bottom: 20px">
              24/7 customer support to assist with bookings, changes, and any
              travel-related inquiries.
            </p>
            <a href="contact.html" class="btn">Get Help</a>
          </div>
        </div>
      </div>
    </section>

    <section
      class="service-features"
      style="padding: 60px 0; background-color: #f5f5f5"
    >
      <div class="container">
        <div class="section-title">
          <h2>Why Choose Our Services?</h2>
          <p>
            We go the extra mile to ensure your journey is safe, comfortable,
            and enjoyable
          </p>
        </div>

        <div
          class="features-grid"
          style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
          "
        >
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">Safety First</h3>
              <p style="color: #7f8c8d">
                Our buses are regularly maintained and our drivers are highly
                trained professionals with excellent safety records.
              </p>
            </div>
          </div>
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-clock"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">Punctuality</h3>
              <p style="color: #7f8c8d">
                We pride ourselves on maintaining strict schedules with a 95%
                on-time departure and arrival record.
              </p>
            </div>
          </div>
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-smile"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">Comfort</h3>
              <p style="color: #7f8c8d">
                Spacious, reclining seats with ample legroom and climate control
                for a comfortable journey.
              </p>
            </div>
          </div>
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-rupee-sign"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">
                Affordable Prices
              </h3>
              <p style="color: #7f8c8d">
                Competitive pricing with regular discounts and special offers
                for frequent travelers.
              </p>
            </div>
          </div>
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-map-marked-alt"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">
                Extensive Network
              </h3>
              <p style="color: #7f8c8d">
                With over 50 destinations across Nepal, we can take you almost
                anywhere you need to go.
              </p>
            </div>
          </div>
          <div
            class="feature-item"
            style="
              display: flex;
              align-items: flex-start;
              background-color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            "
          >
            <div style="margin-right: 15px; color: #f39c12; font-size: 24px">
              <i class="fas fa-undo"></i>
            </div>
            <div>
              <h3 style="margin-bottom: 10px; color: #2c3e50">
                Flexible Cancellation
              </h3>
              <p style="color: #7f8c8d">
                Easy cancellation and rescheduling options with minimal fees
                when plans change.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section
      class="cta-section"
      style="
        padding: 60px 0;
        background-color: #2c3e50;
        color: white;
        text-align: center;
      "
    >
      <div class="container">
        <h2 style="margin-bottom: 20px">Ready to Travel With Us?</h2>
        <p
          style="
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
          "
        >
          Book your bus ticket today and experience comfortable, reliable travel
          across Nepal.
        </p>
        <a
          href="routes.php"
          class="btn"
          style="background-color: #f39c12; color: white"
          >Book Your Ticket Now</a
        >
      </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="main.js"></script>
  </body>
</html>
