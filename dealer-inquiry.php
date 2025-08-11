<?php
// dealer-inquiry.php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

$page_title = 'Become a Dealer – Partner with Warung Pumps';

$errors = [];
$sent = false;

// sticky values
$name = $mobile = $location = $type = $experience = $query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) {
    http_response_code(400);
    $errors[] = 'Invalid request. Please refresh and try again.';
  } else {
    // Collect
    $name       = trim($_POST['name'] ?? '');
    $mobile_raw = (string)($_POST['mobile'] ?? '');
    $mobile     = preg_replace('/\D+/', '', $mobile_raw); // digits only
    $location   = trim($_POST['location'] ?? '');
    $type       = trim($_POST['type'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $query      = trim($_POST['query'] ?? '');

    // Validate
    if ($name === '') $errors[] = 'Name is required.';
    if ($mobile === '' || strlen($mobile) < 7 || strlen($mobile) > 15) $errors[] = 'Please enter a valid mobile number.';
    if ($location === '') $errors[] = 'Location / City is required.';
    if ($type === '' || $type === 'Type of Applicant') $errors[] = 'Please select your applicant type.';

    if (!$errors) {
      // Create table if not exists (safe/idempotent)
      $mysqli->query("
        CREATE TABLE IF NOT EXISTS dealer_applications (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(150) NOT NULL,
          mobile VARCHAR(20) NOT NULL,
          location VARCHAR(150) NOT NULL,
          type VARCHAR(80) NOT NULL,
          experience VARCHAR(80) NULL,
          query TEXT NULL,
          created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
      ");

      $stmt = $mysqli->prepare("INSERT INTO dealer_applications (name, mobile, location, type, experience, query, created_at) VALUES (?,?,?,?,?,?,NOW())");
      if ($stmt) {
        $stmt->bind_param('ssssss', $name, $mobile, $location, $type, $experience, $query);
        $ok = $stmt->execute();
        $stmt->close();
        if ($ok) {
          $sent = true;
          $name = $mobile = $location = $type = $experience = $query = '';
        } else {
          $errors[] = 'Could not submit your application. Please try again.';
        }
      } else {
        $errors[] = 'Server error while preparing your request.';
      }
    }
  }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Section 1: Banner -->
<section class="hero dealer-hero">
  <div class="hero-content">
    <h1>Partner with Warung Pumps</h1>
    <p>Start or grow your business with India’s trusted pump brand.</p>
    <a href="#apply" class="btn" style="margin-top: 16px;">Apply for Dealership →</a>
  </div>
</section>

<!-- Messages -->
<div class="container" style="max-width: 960px;">
  <?php if ($sent): ?>
    <div class="alert success">Thanks! Your application has been submitted. Our team will reach out shortly.</div>
  <?php elseif ($errors): ?>
    <div class="alert error"><?php echo e(implode('<br>', $errors)); ?></div>
  <?php endif; ?>
</div>

<!-- Section 2: Why Become a Dealer -->
<div class="container">
  <h2>Why Become a Dealer?</h2>
  <div class="icon-grid">
    <div class="icon-box">🛒<h3>Fast-Selling Inventory</h3><p>Proven demand across regions</p></div>
    <div class="icon-box">💰<h3>Attractive Margins</h3><p>Profit-friendly pricing structures</p></div>
    <div class="icon-box">🤝<h3>Ongoing Support</h3><p>Training, marketing & stock updates</p></div>
    <div class="icon-box">📦<h3>Reliable Logistics</h3><p>On-time delivery & fulfillment</p></div>
  </div>
</div>

<!-- Section 3: Who Can Apply -->
<div class="container">
  <h2>Who Can Apply?</h2>
  <div class="card-grid">
    <div class="card">
      <h3>Retailers</h3>
      <p>Already selling electrical/mechanical equipment? Expand into pumps.</p>
    </div>
    <div class="card">
      <h3>Entrepreneurs</h3>
      <p>Looking to start a business in a high-demand sector? Let’s talk.</p>
    </div>
    <div class="card">
      <h3>Technicians / Installers</h3>
      <p>Interested in representation in your service area? Get in touch.</p>
    </div>
  </div>
</div>

<!-- Section 4: Map + Testimonials -->
<div class="container">
  <h2>Our Growing Network</h2>
  <div class="map-box">
    <img src="assets/images/india-dealer-map.jpg" alt="Dealer Coverage Map">
  </div>
  <div class="testimonials">
    <div class="testimonial">“Warung gives us great margins and better repeat value.”<br><br>— Rajesh Electricals, UP</div>
    <div class="testimonial">“Stock always arrives on time. Customers know the brand by name.”<br><br>— Desai Traders, MP</div>
  </div>
</div>

<!-- Section 5: Form -->
<div class="container" id="apply">
  <div class="form-section">
    <h2>Apply for Dealership</h2>
    <form action="dealer-inquiry.php#apply" method="post" novalidate>
      <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">

      <input type="text" name="name" placeholder="Your Name" required value="<?php echo e($name); ?>">
      <input type="tel" name="mobile" placeholder="Mobile Number" required value="<?php echo e($mobile); ?>">
      <input type="text" name="location" placeholder="Location / City" required value="<?php echo e($location); ?>">

      <select name="type" required>
        <option value="">Type of Applicant</option>
        <option <?php echo $type==='Retailer'?'selected':''; ?>>Retailer</option>
        <option <?php echo $type==='Service Agent'?'selected':''; ?>>Service Agent</option>
        <option <?php echo $type==='Distributor'?'selected':''; ?>>Distributor</option>
        <option <?php echo $type==='Other'?'selected':''; ?>>Other</option>
      </select>

      <input type="text" name="experience" placeholder="Years in Business (Optional)" value="<?php echo e($experience); ?>">
      <textarea name="query" rows="4" placeholder="Message / Query"><?php echo e($query); ?></textarea>

      <button type="submit" class="btn">Apply for Dealership</button>
    </form>
  </div>
</div>

<!-- Sticky CTA Button -->
<a href="#apply" class="sticky-apply">Apply Now</a>

<?php include __DIR__ . '/includes/footer.php'; ?>
