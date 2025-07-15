<?php 
include 'header.php'; 
include 'config.php'; 

$success = $error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($name && $email && $subject && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            header("Location: contact.php?sent=1");
            exit;
        } else {
            $error = "Failed to send message.";
        }
    } else {
        $error = "All fields are required.";
    }
}

if (isset($_GET['sent'])) {
    $success = "Message sent successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | ebus Nepal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<section class="page-hero">
  <div class="container">
    <h1>Contact Us</h1>
    <p>We're here to help and answer any questions you might have</p>
  </div>
</section>

<section class="contact-section" style="padding: 60px 0">
  <div class="container">
    <div class="contact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
      
     
      <div class="contact-info" style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
        <h2 style="margin-bottom: 20px; color: #2c3e50">Get in Touch</h2>
        <div class="info-item" style="display: flex; align-items: flex-start; margin-bottom: 20px;">
          <div style="margin-right: 15px; color: #f39c12; font-size: 20px"><i class="fas fa-map-marker-alt"></i></div>
          <div>
            <h3 style="margin-bottom: 5px; color: #2c3e50">Address</h3>
            <p style="color: #7f8c8d">ebus Nepal Pvt. Ltd., Pokhara, Nepal</p>
          </div>
        </div>
        <div class="info-item" style="display: flex; align-items: flex-start; margin-bottom: 20px;">
          <div style="margin-right: 15px; color: #f39c12; font-size: 20px"><i class="fas fa-phone"></i></div>
          <div>
            <h3 style="margin-bottom: 5px; color: #2c3e50">Phone</h3>
            <p style="color: #7f8c8d">+977 1 1234567<br />+977 1 7654321</p>
          </div>
        </div>
        <div class="info-item" style="display: flex; align-items: flex-start; margin-bottom: 20px;">
          <div style="margin-right: 15px; color: #f39c12; font-size: 20px"><i class="fas fa-envelope"></i></div>
          <div>
            <h3 style="margin-bottom: 5px; color: #2c3e50">Email</h3>
            <p style="color: #7f8c8d">info@ebusnepal.com<br />support@ebusnepal.com</p>
          </div>
        </div>
        <div class="info-item" style="display: flex; align-items: flex-start;">
          <div style="margin-right: 15px; color: #f39c12; font-size: 20px"><i class="fas fa-clock"></i></div>
          <div>
            <h3 style="margin-bottom: 5px; color: #2c3e50">Working Hours</h3>
            <p style="color: #7f8c8d">Sunday-Friday: 7:00 AM - 7:00 PM<br />Saturday: 9:00 AM - 5:00 PM</p>
          </div>
        </div>
      </div>

    
      <div class="contact-form" style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
        <h2 style="margin-bottom: 20px; color: #2c3e50">Send Us a Message</h2>

        
        <?php if ($success): ?>
          <p style="color: green;"><?= $success ?></p>
        <?php elseif ($error): ?>
          <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <form id="contactForm" method="POST" style="display: grid; gap: 15px">
          <div class="form-group" style="display: flex; flex-direction: column">
            <label for="name" style="margin-bottom: 8px; font-weight: 500; color: #333">Your Name</label>
            <input type="text" id="name" name="name" required style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;" />
          </div>
          <div class="form-group" style="display: flex; flex-direction: column">
            <label for="email" style="margin-bottom: 8px; font-weight: 500; color: #333">Your Email</label>
            <input type="email" id="email" name="email" required style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;" />
          </div>
          <div class="form-group" style="display: flex; flex-direction: column">
            <label for="subject" style="margin-bottom: 8px; font-weight: 500; color: #333">Subject</label>
            <input type="text" id="subject" name="subject" required style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;" />
          </div>
          <div class="form-group" style="display: flex; flex-direction: column">
            <label for="message" style="margin-bottom: 8px; font-weight: 500; color: #333">Message</label>
            <textarea id="message" name="message" rows="5" required style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;"></textarea>
          </div>
          <button type="submit" class="btn">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Map -->
<section class="map-section" style="padding: 0 0 60px">
  <div class="container">
    <div style="height: 400px; background-color: #eee; border-radius: 8px; overflow: hidden;">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.456715614864!2d85.3207436155259!3d27.70554438279396!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19a64b5f13e1%3A0x28b2d0eacda46b98!2sKathmandu%2044600%2C%20Nepal!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus"
        width="100%" height="100%" style="border: 0" allowfullscreen="" loading="lazy"
      ></iframe>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="main.js"></script>

</body>
</html>
