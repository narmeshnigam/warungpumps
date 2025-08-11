<?php
// why-choose-us.php – Warung Pumps
$root = __DIR__;
$page_title = 'Why Warung Pumps | Trusted Water Pump Brand in India';
$page_description = 'Discover why Warung Pumps is trusted across farms, homes, and industry. See our quality, support, and performance commitments.';
$headerPath = $root . '/includes/header.php';
$footerPath = $root . '/includes/footer.php';
?>
<?php if (file_exists($headerPath)) { include $headerPath; } else { ?>
  <div id="header"></div>
<?php } ?>

<!-- Hero -->
<section class="wp-why-hero">
  <h1>Built for Performance. Trusted by People.</h1>
  <p>Every Warung Pump is a commitment to reliability, service, and satisfaction.</p>
</section>

<div class="container wp-why">
  <!-- 4-Point Advantage -->
  <h2>Why People Choose Warung Pumps</h2>
  <div class="grid4">
    <div class="card"><span class="emoji">⚙️</span><h3>Rugged Engineering</h3><p>Heavy-duty build quality designed for rural and industrial conditions.</p></div>
    <div class="card"><span class="emoji">🔧</span><h3>End-to-End Support</h3><p>We assist at selection, installation, service, and replacement.</p></div>
    <div class="card"><span class="emoji">📈</span><h3>Performance Guaranteed</h3><p>Every model is tested for discharge rate and depth claims.</p></div>
    <div class="card"><span class="emoji">💡</span><h3>Affordable Excellence</h3><p>Competitively priced without compromising quality.</p></div>
  </div>
</div>

<!-- Trust Milestones -->
<div class="milestone-strip">
  <div class="milestone">
    <h3>25,000+</h3>
    <p>Pumps Installed</p>
  </div>
  <div class="milestone">
    <h3>100+</h3>
    <p>Dealers Across India</p>
  </div>
  <div class="milestone">
    <h3>7-Day</h3>
    <p>WhatsApp & Call Support</p>
  </div>
  <div class="milestone">
    <h3>85%</h3>
    <p>Repeat Customers in Farming</p>
  </div>
</div>

<!-- Quote Block -->
<div class="quote-block">
  <p>“Our customers trust Warung Pumps because we deliver what we promise — performance, affordability, and fast service.”<strong>— Warung Support Team</strong></p>
</div>

<!-- CTA -->
<div class="container center wp-why-cta">
  <a href="/products.php" class="btn">Browse Pump Models →</a>
</div>

<a href="https://wa.me/918292397155?text=Hi%2C+I%27d+like+to+know+why+Warung+is+trusted" class="sticky-whatsapp"><span class="emoji">💬</span> Ask Us Why We’re Trusted</a>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?>
  <div id="footer"></div>
<?php } ?>
