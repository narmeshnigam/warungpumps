<?php
// privacy.php – Warung Pumps Privacy & Terms page
$root = __DIR__;
$page_title = 'Privacy Policy & Terms – Warung Pumps';
$page_description = 'Read how Warung Pumps protects your privacy and governs the use of its website. Learn about data handling, third-party tools, terms of usage, and liability disclaimers.';
$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<?php if (file_exists($headerPath)) { include $headerPath; } else { ?>
  <div id="header"></div>
<?php } ?>

<div class="wp-legal container">
  <h1>Privacy Policy & Terms of Use</h1>

  <div class="section">
    <h2>Privacy Policy</h2>
    <p><strong>Information Collected:</strong> We collect your name, phone number, city/location, and any messages you submit via our website forms.</p>
    <p><strong>Usage of Information:</strong> Your data is used only to respond to inquiries, provide service, and occasionally inform you of offers (opt-out options available).</p>
    <p><strong>Storage & Protection:</strong> We store your data securely and do not sell, share, or misuse it under any circumstances.</p>
    <p><strong>Third-Party Tools:</strong> We use tools like Google Analytics and WhatsApp APIs for tracking and communication. These follow their own policies.</p>
  </div>

  <div class="cookie-box">
    <strong>Cookie Usage:</strong> We may use cookies to enhance user experience and track analytics. You can control preferences via your browser settings.
  </div>

  <div class="section">
    <h2>Terms of Use</h2>
    <p><strong>Accuracy Disclaimer:</strong> Product specifications, visuals, and availability are subject to change without prior notice.</p>
    <p><strong>External Links:</strong> We may link to external websites such as YouTube or Google Maps. We are not responsible for their content or policies.</p>
    <p><strong>Liability:</strong> Warung Pumps shall not be liable for any damages arising from website use, technical issues, or form-related errors.</p>
  </div>

  <div class="section">
    <h2>Copyright & Trademark</h2>
    <p>All text, images, logos, and downloadable files on this website are either the property of Warung Pumps or its content licensors. No reproduction or redistribution is allowed without written consent.</p>
  </div>

  <p class="footer-note">Have questions about our policy? <a href="/contact.php" class="cta">Contact Us →</a></p>
</div>

<a href="https://wa.me/918292397155?text=Hi%2C+I+have+a+question+about+your+privacy+terms" class="sticky-whatsapp"><span class="emoji">💬</span> Privacy Help</a>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?>
  <div id="footer"></div>
<?php } ?>
