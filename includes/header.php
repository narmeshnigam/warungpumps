<?php
// includes/header.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Warung Pumps | Tubewell, Submersible & Openwell Pumps in India'; ?></title>
  <?php $meta = isset($page_description) ? $page_description : 'Warung Pumps offers reliable, high-performance water pumps for agriculture, residential, and commercial use. Explore our tubewell, submersible, and openwell pump range today.'; ?>
  <meta name="description" content="<?php echo htmlspecialchars($meta, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- Site Header / Navigation -->
<header class="site-header">
  <a href="/index.php" class="logo" style="display:flex;align-items:center;gap:10px;">
    <img src="/assets/images/warung-pumps-logo.png" alt="Warung Pumps" style="height:40px;">
  </a>

  <button id="menu-toggle" aria-label="Toggle menu" style="display:none;background:transparent;border:0;font-size:28px;line-height:1;cursor:pointer;">☰</button>

  <nav class="main-nav" id="main-nav" style="position:relative;">
    <a href="/index.php">Home</a>

    <div class="dropdown" style="position:relative;">
      <a href="/products.php" style="display:inline-block;">Products ▾</a>
      <ul class="dropdown-menu">
        <li><a href="/products.php?type=Submersible">Submersible Pumps</a></li>
        <li><a href="/products.php?type=Tubewell">Tubewell Pumps</a></li>
        <li><a href="/products.php?type=Openwell">Openwell Pumps</a></li>
        <li><a href="/products.php?type=Borewell%20Accessories">Borewell Accessories</a></li>
      </ul>
    </div>

    <a href="/applications.php">Applications</a>
    <a href="/gallery.php">Gallery</a>
    <a href="/why-choose-us.php">Why Choose Us</a>
    <a href="/faqs.php">FAQs</a>
    <a href="/dealer-inquiry.php">Dealer</a>

    <a href="/dealer-inquiry.php" class="btn" style="margin-left:6px;">Become a Dealer</a>
    <a href="/contact.php" class="btn" style="margin-left:6px;background:#fff;color:var(--blue);border:1px solid var(--blue);">Contact</a>
  </nav>
</header>

<script>
// Mobile nav toggle
(function(){
  const toggle = document.getElementById('menu-toggle');
  const nav = document.getElementById('main-nav');
  if (!toggle || !nav) return;
  toggle.addEventListener('click', () => {
    nav.classList.toggle('open');
  });
})();
</script>
