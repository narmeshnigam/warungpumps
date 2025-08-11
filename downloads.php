<?php
// downloads.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$page_title = 'Download Warung Pump Brochures, Manuals & Tech Sheets';

// Ensure table exists (idempotent)
$mysqli->query("
  CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category ENUM('Brochures','Technical Sheets','Installation Manuals','Warranty & Support Docs') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    published TINYINT(1) NOT NULL DEFAULT 1,
    downloads_count INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Fetch published docs
$docs = [];
$res = $mysqli->query("SELECT id, title, description, category, file_path, downloads_count FROM documents WHERE published=1 ORDER BY created_at DESC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $docs[] = $row;
  }
  $res->free();
}

// Categories for tabs
$categories = ['Brochures','Technical Sheets','Installation Manuals','Warranty & Support Docs'];

include __DIR__ . '/includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero downloads-hero">
  <h1>Brochures, Manuals & More — In One Place</h1>
  <p>Quickly download the technical documents you need.</p>
  <a href="selector-tool.html" class="btn" style="margin-top: 16px;">Get Help Choosing a Model →</a>
</section>

<!-- Section 2: Tabs -->
<div class="container sticky-top">
  <div class="tabs" id="download-tabs">
    <?php foreach ($categories as $i => $cat): ?>
      <button class="tab<?php echo $i===0?' active':''; ?>" data-cat="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
    <?php endforeach; ?>
    <button class="tab" data-cat="All">All</button>
  </div>
</div>

<!-- Section 3: File Grid -->
<div class="container">
  <div class="grid" id="download-grid">
    <?php if (count($docs)): ?>
      <?php foreach ($docs as $d): ?>
        <div class="card" data-cat="<?php echo e($d['category']); ?>">
          <h3><?php echo e($d['title']); ?></h3>
          <?php if (!empty($d['description'])): ?>
            <p><?php echo e($d['description']); ?></p>
          <?php endif; ?>
          <a class="btn download" href="download.php?id=<?php echo (int)$d['id']; ?>" target="_blank" rel="noopener">Download PDF</a>
          <p style="margin-top:8px;font-size:12px;color:#777;">Downloads: <?php echo (int)$d['downloads_count']; ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Fallback static cards if DB has no rows yet -->
      <div class="card" data-cat="Technical Sheets">
        <h3>WRG-2HP-SUB Data Sheet</h3>
        <p>Includes specs, material, and performance curve overview.</p>
        <a href="assets/docs/wrg-2hp-sub-datasheet.pdf" class="btn download" target="_blank">Download PDF</a>
      </div>
      <div class="card" data-cat="Brochures">
        <h3>Warung Pump Product Brochure</h3>
        <p>Overview of all models with features and category benefits.</p>
        <a href="assets/docs/warung-brochure.pdf" class="btn download" target="_blank">Download PDF</a>
      </div>
      <div class="card" data-cat="Installation Manuals">
        <h3>Installation Manual – WRG Series</h3>
        <p>Step-by-step setup and maintenance instructions.</p>
        <a href="assets/docs/wrg-installation-guide.pdf" class="btn download" target="_blank">Download PDF</a>
      </div>
      <div class="card" data-cat="Warranty & Support Docs">
        <h3>Warranty & Support Policy</h3>
        <p>Coverage, conditions, and customer care contacts.</p>
        <a href="assets/docs/warung-warranty-policy.pdf" class="btn download" target="_blank">Download PDF</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Section 4: Help CTA -->
<div class="container support-banner">
  <h2>Need help understanding a technical sheet or choosing the right product?</h2>
  <a href="https://wa.me/918292397155?text=Hi%2C+I+need+help+with+Warung+product+documents" class="btn" style="margin-top: 12px;">Talk to an Expert →</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Simple tabs filter -->
<script>
  const tabs = document.querySelectorAll('#download-tabs .tab');
  const cards = document.querySelectorAll('#download-grid .card');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const cat = tab.dataset.cat;
      cards.forEach(c => {
        const show = (cat === 'All') || (c.dataset.cat === cat);
        c.style.display = show ? '' : 'none';
      });
    });
  });
</script>
