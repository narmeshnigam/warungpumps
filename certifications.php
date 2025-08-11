<?php
$page_title = 'Certified Quality Pumps | ISI, BIS, ISO, CE – Warung Pumps';
include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<div class="hero certifications-hero">
  <h1>Certified to Perform, Built to Last</h1>
  <p>Our products meet national and international quality benchmarks.</p>
  <div class="logo-strip">
    <img src="assets/logos/iso.png" alt="ISO 9001">
    <img src="assets/logos/isi.png" alt="ISI Approved">
    <img src="assets/logos/bis.png" alt="BIS Mark">
    <img src="assets/logos/ce.png" alt="CE Marking">
  </div>
</div>

<!-- Certification Cards -->
<div class="container">
  <div class="cert-grid">
    <div class="cert-card">
      <h3>ISO 9001:2015 Certificate</h3>
      <p>Manufacturing & testing process certification.</p>
      <a href="assets/certificates/iso9001.pdf" target="_blank">Download PDF</a>
    </div>
    <div class="cert-card">
      <h3>ISI Mark Approval</h3>
      <p>Domestic submersible & openwell motors approved.</p>
      <a href="assets/certificates/isi.pdf" target="_blank">Download PDF</a>
    </div>
    <div class="cert-card">
      <h3>CE Compliance Declaration</h3>
      <p>All export-ready models certified for CE compliance.</p>
      <a href="assets/certificates/ce.pdf" target="_blank">Download PDF</a>
    </div>
  </div>
</div>

<!-- Accordion Section -->
<div class="container accordion">
  <h2 style="text-align: center; margin-bottom: 24px;">Our Testing & Quality Control Process</h2>
  <div class="accordion-item">
    <div class="accordion-title">Incoming Material Check</div>
    <div class="accordion-content"><p>Every batch of copper, casting, and shafts is verified for conductivity, strength, and quality.</p></div>
  </div>
  <div class="accordion-item">
    <div class="accordion-title">Winding & Assembly Test</div>
    <div class="accordion-content"><p>Torque, seal fitment, insulation checks, and voltage stability tests at mid-stage.</p></div>
  </div>
  <div class="accordion-item">
    <div class="accordion-title">Final Discharge Test</div>
    <div class="accordion-content"><p>Measured flow rate and head vs rated specs before packing and tagging.</p></div>
  </div>
  <div class="accordion-item">
    <div class="accordion-title">Random Batch Sampling</div>
    <div class="accordion-content"><p>Re-testing of tagged units for repeat accuracy from store samples.</p></div>
  </div>
</div>

<!-- Testing Video -->
<div class="container video-section">
  <h2>Watch Our Pumps Being Tested</h2>
  <iframe src="https://www.youtube.com/embed/TESTING_VIDEO" title="Pump Quality Testing"></iframe>
</div>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+please+share+Warung+Pump+certifications" class="sticky-whatsapp"><span class="emoji">📄</span> Ask About Certifications</a>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Accordion Toggle Script -->
<script>
  document.querySelectorAll('.accordion-title').forEach(item => {
    item.addEventListener('click', () => {
      const content = item.nextElementSibling;
      const active = item.classList.contains('active');
      document.querySelectorAll('.accordion-content').forEach(c => c.style.maxHeight = null);
      document.querySelectorAll('.accordion-title').forEach(t => t.classList.remove('active'));
      if (!active) {
        content.style.maxHeight = content.scrollHeight + 'px';
        item.classList.add('active');
      }
    });
  });
</script>
