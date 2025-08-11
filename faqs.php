<?php
// faqs.php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

$page_title = 'Frequently Asked Questions – Warung Pumps';

/* Ensure table exists (idempotent) */
$mysqli->query("
  CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(80) NOT NULL DEFAULT 'General',
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    published TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* Fetch published FAQs */
$faqs = [];
$res = $mysqli->query("SELECT id, category, question, answer FROM faqs WHERE published=1 ORDER BY sort_order ASC, created_at DESC");
if ($res) {
  while ($row = $res->fetch_assoc()) $faqs[] = $row;
  $res->free();
}

/* Collect categories (preserve order) */
$cats = [];
foreach ($faqs as $f) {
  $c = $f['category'] ?: 'General';
  if (!in_array($c, $cats, true)) $cats[] = $c;
}
if (!$cats) $cats = ['General']; // for fallback

include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero faqs-hero">
  <div class="hero-content">
    <h1>Frequently Asked Questions</h1>
    <p>Quick answers about models, installation, service, and warranties.</p>
  </div>
</section>

<!-- Category Pills -->
<div class="container">
  <div class="faq-pills" id="faq-pills">
    <?php foreach ($cats as $i => $c): ?>
      <button class="pill<?php echo $i===0?' active':''; ?>" data-cat="<?php echo e($c); ?>"><?php echo e($c); ?></button>
    <?php endforeach; ?>
    <?php if (count($cats) > 1): ?>
      <button class="pill" data-cat="All">All</button>
    <?php endif; ?>
  </div>
</div>

<!-- FAQ List -->
<div class="container">
  <div class="accordion" id="faq-accordion">
    <?php if ($faqs): ?>
      <?php foreach ($faqs as $i => $f): ?>
        <div class="accordion-item" data-cat="<?php echo e($f['category'] ?: 'General'); ?>">
          <button class="accordion-title" aria-expanded="<?php echo $i===0?'true':'false'; ?>">
            <?php echo e($f['question']); ?>
            <span class="icon">+</span>
          </button>
          <div class="accordion-content"<?php echo $i===0?' style="max-height:300px;"':''; ?>>
            <p><?php echo nl2br(e($f['answer'])); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Fallback FAQs (no DB rows yet) -->
      <?php
        $fallback = [
          ['General','Do you provide installation?',"Yes, installation is available in selected regions. Contact support for availability."],
          ['Products','What’s the minimum bore depth for submersible pumps?',"We recommend a minimum depth of ~100 ft for most WRG‑SUB series."],
          ['Warranty','What is the standard warranty?',"Submersible: 24 months. Tubewell: 18 months. See policy document for terms."],
          ['Service','Where can I get spare parts?',"At authorized service centers and dealers. You can also enquire via WhatsApp."],
          ['Selection','How do I choose the right pump?',"Use our Selector Tool or call us with your bore depth and discharge needs."],
        ];
        foreach ($fallback as $i => $f) {
      ?>
        <div class="accordion-item" data-cat="<?php echo e($f[0]); ?>">
          <button class="accordion-title" aria-expanded="<?php echo $i===0?'true':'false'; ?>">
            <?php echo e($f[1]); ?><span class="icon">+</span>
          </button>
          <div class="accordion-content"<?php echo $i===0?' style="max-height:300px;"':''; ?>>
            <p><?php echo e($f[2]); ?></p>
          </div>
        </div>
      <?php } ?>
    <?php endif; ?>
  </div>
</div>

<!-- CTA Strip -->
<div class="container cta-strip">
  <h2>Didn’t find what you were looking for?</h2>
  <a href="contact.php" class="btn" style="margin-top: 12px;">Ask Our Team →</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Interactions -->
<script>
  // Pills filter
  const pills = document.querySelectorAll('#faq-pills .pill');
  const items = document.querySelectorAll('#faq-accordion .accordion-item');
  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('active'));
    p.classList.add('active');
    const cat = p.dataset.cat;
    items.forEach(it => {
      const show = (cat === 'All') || (it.dataset.cat === cat);
      it.style.display = show ? '' : 'none';
    });
  }));

  // Accordion toggle
  document.querySelectorAll('.accordion-title').forEach(btn => {
    btn.addEventListener('click', () => {
      const open = btn.getAttribute('aria-expanded') === 'true';
      // Close others
      document.querySelectorAll('.accordion-title').forEach(b => {
        b.setAttribute('aria-expanded','false');
        b.closest('.accordion-item').querySelector('.accordion-content').style.maxHeight = null;
        b.querySelector('.icon').textContent = '+';
      });
      if (!open) {
        const content = btn.closest('.accordion-item').querySelector('.accordion-content');
        content.style.maxHeight = content.scrollHeight + 'px';
        btn.setAttribute('aria-expanded', 'true');
        btn.querySelector('.icon').textContent = '–';
      }
    });
  });
</script>
