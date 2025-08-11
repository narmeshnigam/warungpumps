<?php
// contact.php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

$page_title = 'Contact Warung Pumps | Store Location, Enquiry & Support';

$errors = [];
$sent = false;

// Sticky form vars
$name = $mobile = $location = $interest = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  if (!csrf_verify($_POST['csrf'] ?? '')) {
    http_response_code(400);
    $errors[] = 'Invalid request. Please refresh and try again.';
  } else {
    // Collect
    $name     = trim($_POST['name'] ?? '');
    $mobile   = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? '')); // digits only
    $location = trim($_POST['location'] ?? '');
    $interest = trim($_POST['interest'] ?? '');
    $message  = trim($_POST['message'] ?? '');

    // Validate
    if ($name === '') $errors[] = 'Name is required.';
    if ($mobile === '' || strlen($mobile) < 7 || strlen($mobile) > 15) $errors[] = 'Please enter a valid mobile number.';
    if ($location === '') $errors[] = 'City / Location is required.';
    if ($interest === '') $errors[] = 'Please select what you’re interested in.';

    // Save
    if (!$errors) {
      // Create table if not exists (safe, idempotent)
      $mysqli->query("
        CREATE TABLE IF NOT EXISTS contact_enquiries (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(150) NOT NULL,
          mobile VARCHAR(20) NOT NULL,
          location VARCHAR(150) NOT NULL,
          interest VARCHAR(80) NOT NULL,
          message TEXT,
          created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
      ");

      $stmt = $mysqli->prepare("INSERT INTO contact_enquiries (name, mobile, location, interest, message, created_at) VALUES (?,?,?,?,?,NOW())");
      if ($stmt) {
        $stmt->bind_param('sssss', $name, $mobile, $location, $interest, $message);
        $ok = $stmt->execute();
        $stmt->close();
        if ($ok) {
          $sent = true;
          // Clear sticky values
          $name = $mobile = $location = $interest = $message = '';
        } else {
          $errors[] = 'Could not submit your enquiry. Please try again.';
        }
      } else {
        $errors[] = 'Server error while preparing your request.';
      }
    }
  }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner -->
<section class="hero contact-hero">
  <div class="hero-content">
    <h1>We’re Here to Help You</h1>
    <p>Reach out for product advice, store location, or service help.</p>
  </div>
</section>

<!-- Messages -->
<div class="container" style="max-width: 960px;">
  <?php if ($sent): ?>
    <div class="alert success">Thanks! Your enquiry has been submitted. Our team will contact you shortly.</div>
  <?php elseif ($errors): ?>
    <div class="alert error"><?php echo e(implode('<br>', $errors)); ?></div>
  <?php endif; ?>
</div>

<!-- Contact Info Grid -->
<div class="container">
  <div class="info-grid">
    <div class="info-box"><span class="emoji">📍</span><h3>Address</h3><p>81, Tekari Road, Gol Bagicha, Gaya - 823001</p></div>
    <div class="info-box"><span class="emoji">☎</span><h3>Phone</h3><p><a href="tel:+918292397155">+91 8292397155</a></p></div>
    <div class="info-box"><span class="emoji">💬</span><h3>WhatsApp</h3><p><a href="https://wa.me/918292397155?text=Hi%2C+I+need+help+with+pumps">Chat Now</a></p></div>
    <div class="info-box"><span class="emoji">✉️</span><h3>Email</h3><p><a href="mailto:contact@warungpumps.com">contact@warungpumps.com</a></p></div>
    <div class="info-box"><span class="emoji">🕒</span><h3>Timings</h3><p>Mon–Sat, 10:00 AM – 6:30 PM</p></div>
  </div>
</div>

<!-- Google Map -->
<div class="container">
  <h2 style="text-align: center; margin-top: 48px;">Visit Us at Our Store</h2>
  <iframe src="https://maps.google.com/maps?q=Gaya%2C%20Bihar&t=&z=15&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
</div>

<!-- Contact Form -->
<div class="container">
  <form action="contact.php" method="post" novalidate>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <h2 style="text-align: center;">Submit Your Enquiry</h2>

    <input type="text" name="name" placeholder="Your Name" required value="<?php echo e($name); ?>">
    <input type="tel" name="mobile" placeholder="Mobile Number" required value="<?php echo e($mobile); ?>">
    <input type="text" name="location" placeholder="City / Location" required value="<?php echo e($location); ?>">

    <select name="interest" required>
      <option value="">Interested In</option>
      <option <?php echo $interest==='Product Inquiry'?'selected':''; ?>>Product Inquiry</option>
      <option <?php echo $interest==='Service Support'?'selected':''; ?>>Service Support</option>
      <option <?php echo $interest==='Dealer Inquiry'?'selected':''; ?>>Dealer Inquiry</option>
      <option <?php echo $interest==='General'?'selected':''; ?>>General</option>
    </select>

    <textarea name="message" rows="4" placeholder="Your Message"><?php echo e($message); ?></textarea>
    <button type="submit" class="btn">Submit Enquiry</button>
  </form>
</div>

<!-- FAQ Section -->
<div class="container faq-section">
  <h2>Frequently Asked Questions</h2>
  <div class="faq">
    <h3>What’s the minimum bore depth for your submersible pumps?</h3>
    <p>We recommend a minimum depth of 100ft for most WRG-SUB series pumps.</p>
  </div>
  <div class="faq">
    <h3>Do you provide installation?</h3>
    <p>Yes, installation is available in selected regions. Please contact support for availability.</p>
  </div>
  <div class="faq">
    <h3>Where can I get spare parts?</h3>
    <p>Spare parts are available at authorized service centers and dealers. You can also inquire via WhatsApp.</p>
  </div>
</div>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+I+want+to+connect+with+Warung+Pumps" class="sticky-whatsapp"><span class="emoji">💬</span> Chat on WhatsApp</a>

<?php include __DIR__ . '/includes/footer.php'; ?>
