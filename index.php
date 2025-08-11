<?php
// index.php
$page_title = 'Warung Pumps | Tubewell, Submersible & Openwell Pumps in India';
include __DIR__ . '/includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero">
  <div class="hero-content">
    <h1>Pump Power You Can Trust</h1>
    <p style="font-size: 20px; margin: 16px 0;">Tubewell & Submersible Pumps for Farm, Home & Industry</p>
    <a href="/products.php?type=Submersible" class="btn" style="margin-right: 16px;">Browse Pump Range</a>
    <a href="/contact.php" class="btn" style="background: rgba(255,255,255,0.8); color: var(--blue);">Locate Our Store</a>
  </div>
</section>

<!-- Section 2: Quick Product Highlights -->
<section class="section">
  <div class="container">
    <h2 class="text-center">Explore Our Pump Range</h2>
    <div class="grid3">
      <div class="card">
        <img src="assets/images/icon-tubewell.png" alt="Tubewell Icon" style="height: 64px; margin-bottom: 12px;">
        <h3>Tubewell Pumps</h3>
        <p>Long-lasting, deep suction systems</p>
        <a href="/products.php?type=Tubewell" class="btn" style="margin-top: 12px;">View Models →</a>
      </div>
      <div class="card">
        <img src="assets/images/icon-submersible.png" alt="Submersible Icon" style="height: 64px; margin-bottom: 12px;">
        <h3>Submersible Pumps</h3>
        <p>Efficient for deep borewells</p>
        <a href="/products.php?type=Submersible" class="btn" style="margin-top: 12px;">View Models →</a>
      </div>
      <div class="card">
        <img src="assets/images/icon-openwell.png" alt="Openwell Icon" style="height: 64px; margin-bottom: 12px;">
        <h3>Openwell Pumps</h3>
        <p>Ideal for shallow lifting</p>
        <a href="/products.php?type=Openwell" class="btn" style="margin-top: 12px;">View Models →</a>
      </div>
    </div>
  </div>
</section>

<!-- Section 3: Performance Highlights -->
<section class="section highlight">
  <div class="container">
    <h2 class="text-center">Why Choose Warung Pumps</h2>
    <div class="grid4 text-center">
      <div><img src="assets/images/durable.png" style="height:48px;"><p><strong>Field-Tested Durability</strong><br/>Designed to withstand tough rural conditions</p></div>
      <div><img src="assets/images/discharge.png" style="height:48px;"><p><strong>High Discharge Output</strong><br/>Maximum flow at minimum energy use</p></div>
      <div><img src="assets/images/affordable.png" style="height:48px;"><p><strong>Affordable & Efficient</strong><br/>Competitive pricing across all models</p></div>
      <div><img src="assets/images/trust.png" style="height:48px;"><p><strong>Pan-India Trust</strong><br/>Serving customers in 8+ states</p></div>
    </div>
  </div>
</section>

<!-- Section 4: Pump Selector CTA -->
<section class="section" style="background: #E8F0FE;">
  <div class="container text-center">
    <h2>Not Sure What Pump You Need?</h2>
    <p style="max-width: 720px; margin: 0 auto 24px;">Let our intelligent tool recommend the perfect model based on depth, source, usage.</p>
    <a href="/selector-tool.php" class="btn">Try Pump Selector →</a>
  </div>
</section>

<!-- Section 5: Testimonials -->
<section class="section">
  <div class="container">
    <h2 class="text-center">What Our Customers Say</h2>
    <div class="grid3">
      <div class="card"><p class="testimonial">“Installed a Warung pump in 2019 — it’s still running like new!”</p><p>— Rakesh, Aligarh</p></div>
      <div class="card"><p class="testimonial">“Service support was quick and helpful. Got the right model recommended.”</p><p>— Munna Singh, Gonda</p></div>
      <div class="card"><p class="testimonial">“Reliable product for borewell use. I recommend it to all my clients.”</p><p>— Devendra Yadav, Bareilly</p></div>
    </div>
  </div>
</section>

<!-- Section 6: Dealer CTA -->
<section class="section">
  <div class="container" style="display: flex; flex-wrap: wrap; align-items: center; gap: 32px;">
    <img src="assets/images/dealer.png" alt="Dealership" style="flex: 1; max-width: 500px; border-radius: 16px;">
    <div style="flex: 1;">
      <h2>Join Our Dealer Network</h2>
      <p>If you sell pumps or serve customers in your region, partner with Warung Pumps for the best-performing inventory.</p>
    <a href="/dealer-inquiry.php" class="btn" style="margin-top: 12px;">Enquire About Dealership →</a>
    </div>
  </div>
</section>

<!-- Section 7: Visit Us / Store Locator -->
<section class="section highlight">
  <div class="container text-center">
    <h2>See It In Action</h2>
    <p>Visit our store to see live pump demos, compare models, and talk to real technicians.</p>
    <div style="margin: 24px 0;">
      <iframe src="https://maps.google.com/maps?q=Gaya%20823001&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="300" style="border:0; border-radius: 16px;"></iframe>
    </div>
    <a href="/contact.php" class="btn">Locate Our Store →</a>
  </div>
</section>

<!-- Sticky WhatsApp CTA -->
<a href="https://wa.me/918292397155?text=Hi%2C+I+need+help+choosing+a+pump" target="_blank" class="sticky-whatsapp">
  <span class="emoji">💬</span> Need Help Choosing?
</a>

<?php include __DIR__ . '/includes/footer.php'; ?>
