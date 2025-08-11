<?php
// customer_testimonials.php
$page_title = 'Customer Testimonials | Real Reviews – Warung Pumps';
include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero testimonials-hero">
  <div class="hero-content">
    <h1>What Our Customers Say</h1>
    <p>Real stories from farms, homes, and businesses across India.</p>
  </div>
</section>

<!-- Testimonial Slider -->
<div class="container">
  <div class="slider">
    <div class="testimonial-card">
      <img src="assets/images/user1.jpg" alt="Ramesh Yadav">
      <blockquote>“Running strong for 3+ years — no complaints!”</blockquote>
      <div class="name">Ramesh Yadav</div>
      <div class="location">Barabanki (3HP Submersible)</div>
    </div>
    <div class="testimonial-card">
      <img src="assets/images/user2.jpg" alt="Nita Verma">
      <blockquote>“Installed in our home garden. Peaceful and powerful.”</blockquote>
      <div class="name">Nita Verma</div>
      <div class="location">Gorakhpur (1HP Openwell)</div>
    </div>
    <div class="testimonial-card">
      <img src="assets/images/user3.jpg" alt="Zahid Khan">
      <blockquote>“Even after voltage issues, it works flawlessly.”</blockquote>
      <div class="name">Zahid Khan</div>
      <div class="location">Agra (1.5HP Tubewell)</div>
    </div>
    <div class="testimonial-card">
      <img src="assets/images/user4.jpg" alt="Jai Prakash">
      <blockquote>“We recommend Warung to all our clients. Hassle-free.”</blockquote>
      <div class="name">Jai Prakash</div>
      <div class="location">Dealer, Etawah</div>
    </div>
  </div>
</div>

<!-- Featured Videos -->
<div class="container video-section">
  <h2 class="text-center" style="margin-bottom:24px;">Watch Customer Experiences</h2>
  <div class="video-grid">
    <iframe src="https://www.youtube.com/embed/VID1" title="Customer Testimonial 1" allowfullscreen></iframe>
    <iframe src="https://www.youtube.com/embed/VID2" title="Customer Testimonial 2" allowfullscreen></iframe>
    <iframe src="https://www.youtube.com/embed/VID3" title="Customer Testimonial 3" allowfullscreen></iframe>
    <iframe src="https://www.youtube.com/embed/VID4" title="Dealer Feedback" allowfullscreen></iframe>
  </div>
</div>

<!-- CTA Strip -->
<div class="container">
  <div class="cta-banner">
    <p>Have you had a great experience with Warung Pumps? We’d love to hear from you.</p>
    <a href="contact.php" class="btn">Submit Your Feedback →</a>
  </div>
</div>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+I+want+to+share+my+feedback+on+Warung+Pumps" class="sticky-whatsapp">
  <span class="emoji">💬</span> Share Your Review
</a>

<?php include __DIR__ . '/includes/footer.php'; ?>
