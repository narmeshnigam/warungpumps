<?php
// gallery.php — Warung Pumps
// Keeps the same layout/markup as gallery.html, but powers the grid via DB.
// Auto-detects PDO ($pdo) or MySQLi ($conn). Uses config.php/db.php you already have.

declare(strict_types=1);
mb_internal_encoding('UTF-8');

// Bring your config/DB in:
$root = __DIR__;
foreach (['config.php', 'db.php', 'config/db.php'] as $f) {
  $p = $root . '/' . $f;
  if (file_exists($p)) { require_once $p; }
}

// --- Light DB helper that works with PDO or MySQLi ---
function db_is_pdo(): bool { return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO; }
function db_is_mysqli(): bool { return isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli; }
function db_fetch_all(string $sql, array $params = []): array {
  if (db_is_pdo()) {
    $st = $GLOBALS['pdo']->prepare($sql);
    foreach ($params as $k => $v) {
      $type = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $st->bindValue(is_int($k) ? $k+1 : (str_starts_with($k,':')?$k:':'.$k), $v, $type);
    }
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
  } elseif (db_is_mysqli()) {
    $conn = $GLOBALS['conn'];
    // Very small binder; for integers cast, otherwise escape.
    if ($params) {
      foreach ($params as $k => $v) {
        $rep = is_int($v) ? (string)$v : ("'".$conn->real_escape_string((string)$v)."'");
        // replace :key or ? placeholders (basic)
        $sql = preg_replace('/(:'.preg_quote((string)$k,'/').')|\?/', $rep, $sql, 1);
      }
    }
    $res = $conn->query($sql);
    if (!$res) return [];
    $rows = [];
    while ($row = $res->fetch_assoc()) $rows[] = $row;
    return $rows;
  }
  return [];
}
function db_fetch_value(string $sql, array $params = []) {
  $rows = db_fetch_all($sql, $params);
  if (!$rows) return null;
  $row = array_values($rows[0]);
  return $row[0] ?? null;
}

// --- Inputs / defaults ---
$itemsPerPage  = 12;
$placeholder   = 'assets/images/placeholder.jpg';
$catSlug       = isset($_GET['cat']) ? trim((string)$_GET['cat']) : '';
$page          = max(1, (int)($_GET['page'] ?? 1));

// --- Resolve category (optional) ---
$catId = null;
if ($catSlug !== '') {
  $catId = db_fetch_value(
    "SELECT id FROM gallery_categories WHERE slug = :slug AND (is_active = 1 OR is_active IS NULL) LIMIT 1",
    ['slug' => $catSlug]
  );
  if (!$catId) {
    // Invalid slug — show 404 status but still render page with “no images”
    http_response_code(404);
  }
}

// --- Load categories for filter chips ---
$categories = db_fetch_all(
  "SELECT id, name, slug
   FROM gallery_categories
   WHERE (is_active = 1 OR is_active IS NULL)
   ORDER BY COALESCE(display_order,0), name"
);

// --- Count & paginate ---
if ($catId) {
  $totalItems = (int)(db_fetch_value(
    "SELECT COUNT(*) FROM gallery_items WHERE (is_active = 1 OR is_active IS NULL) AND category_id = :cid",
    ['cid' => (int)$catId]
  ) ?? 0);
} else {
  $totalItems = (int)(db_fetch_value(
    "SELECT COUNT(*) FROM gallery_items WHERE (is_active = 1 OR is_active IS NULL)"
  ) ?? 0);
}
$totalPages = max(1, (int)ceil($totalItems / $itemsPerPage));
$offset     = ($page - 1) * $itemsPerPage;

// --- Fetch items for this page ---
$params = ['limit' => $itemsPerPage, 'offset' => $offset];
$sql = "SELECT gi.id, gi.title, gi.alt_text, gi.image_path,
               gc.slug AS cat_slug, gc.name AS cat_name
        FROM gallery_items gi
        LEFT JOIN gallery_categories gc ON gc.id = gi.category_id
        WHERE (gi.is_active = 1 OR gi.is_active IS NULL)";
if ($catId) {
  $sql .= " AND gi.category_id = :cid";
  $params['cid'] = (int)$catId;
}
$sql .= " ORDER BY gi.created_at DESC, gi.id DESC
          LIMIT :limit OFFSET :offset";
$items = db_fetch_all($sql, $params);

// Build query string helper
function qp(array $add = [], array $remove = []): string {
  $q = $_GET ?? [];
  foreach ($remove as $r) unset($q[$r]);
  foreach ($add as $k => $v) $q[$k] = $v;
  $s = http_build_query($q);
  return $s ? '?'.$s : '';
}

// Try to include your header; if not present, keep fallback divs (matches original HTML)
$page_title = 'Pump Installation Gallery & Projects | Warung Pumps';
$page_description = 'Explore real-world installation photos, case studies, and project examples of Warung Pumps in action — from farmlands to industries.';
$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<?php if (file_exists($headerPath)) { include $headerPath; } else { ?>
  <!-- HEADER (fallback to your old loader structure) -->
  <div id="header"></div>
<?php } ?>

<!-- Hero Section -->
<section class="hero gallery-hero">
  <div class="hero-content">
    <h1>See Our Pumps in Action</h1>
    <p>Installations, projects, and proud partners across India.</p>
  </div>
</section>

<div class="container">
  <!-- Filter Tags -->
  <div class="gallery-filter">
    <?php
      $allActive = ($catSlug === '' || !$catId);
      echo '<a class="filter-tag'.($allActive?' active':'').'" href="'.htmlspecialchars(qp([], ['cat','page'])).'">All</a>';
      foreach ($categories as $c) {
        $active = ($catSlug === ($c['slug'] ?? ''));
        $href = qp(['cat' => $c['slug']], ['page']);
        echo '<a class="filter-tag'.($active?' active':'').'" href="'.htmlspecialchars($href).'">'.htmlspecialchars($c['name']).'</a>';
      }
    ?>
  </div>

  <!-- Gallery Grid (DB-driven) -->
  <div class="grid">
    <?php if (!$items): ?>
      <p style="grid-column:1/-1; text-align:center; padding:24px;">No images found<?php echo $catSlug ? ' for this category' : ''; ?> yet.</p>
    <?php else: foreach ($items as $it):
      $img   = $it['image_path'] ?? '';
      $title = $it['title'] ?? '';
      $alt   = $it['alt_text'] ?: ($title ?: 'Warung Pumps project image');
      if (!$img) $img = $placeholder;
    ?>
      <figure class="gallery-img">
        <img src="<?php echo htmlspecialchars($img); ?>"
             alt="<?php echo htmlspecialchars($alt); ?>"
             loading="lazy"
             onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($placeholder); ?>';" />
        <?php if ($title): ?><figcaption style="padding:10px 14px;font-size:14px;"><?php echo htmlspecialchars($title); ?></figcaption><?php endif; ?>
      </figure>
    <?php endforeach; endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Gallery pages">
      <?php
        if ($page > 1) echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$page-1])).'">« Prev</a>';
        $window = 3;
        $start = max(1, $page - $window);
        $end   = min($totalPages, $page + $window);
        if ($start > 1) {
          echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>1])).'">1</a>';
          if ($start > 2) echo '<span class="page-btn" style="pointer-events:none;">…</span>';
        }
        for ($p = $start; $p <= $end; $p++) {
          $cls = 'page-btn'.($p === $page ? ' active' : '');
          echo '<a class="'.$cls.'" href="'.htmlspecialchars(qp(['page'=>$p])).'">'.$p.'</a>';
        }
        if ($end < $totalPages) {
          if ($end < $totalPages - 1) echo '<span class="page-btn" style="pointer-events:none;">…</span>';
          echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$totalPages])).'">'.$totalPages.'</a>';
        }
        if ($page < $totalPages) echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$page+1])).'">Next »</a>';
      ?>
    </nav>
  <?php endif; ?>
</div>

<!-- Project Highlights (kept as in your HTML) -->
<div class="container">
  <h2>Highlighted Projects</h2>
  <div class="project">
    <img src="assets/gallery/project-etawah.jpg" alt="Etawah Submersible">
    <div class="project-content">
      <h3>Submersible Setup for 5 Acre Field – Etawah</h3>
      <p><strong>Pump Used:</strong> WRG-3HP-SUB</p>
      <p><strong>Outcome:</strong> Reduced watering time by 30%, full 2-acre spray range</p>
      <a href="/product-detail.php" class="btn">View Product →</a>
    </div>
  </div>
  <div class="project">
    <img src="assets/gallery/project-kanpur.jpg" alt="Kanpur Tank">
    <div class="project-content">
      <h3>Residential Booster Setup – Kanpur</h3>
      <p><strong>Pump Used:</strong> WRG-1HP-OPEN</p>
      <p><strong>Outcome:</strong> Entire 3-floor tank filled in 10 mins, noise-free</p>
      <a href="/product-detail.php" class="btn">View Product →</a>
    </div>
  </div>
</div>

<!-- Video Section (kept as in your HTML) -->
<div class="container">
  <h2>Watch Our Products in Use</h2>
  <div class="video-grid">
    <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID_1" allowfullscreen></iframe>
    <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID_2" allowfullscreen></iframe>
    <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID_3" allowfullscreen></iframe>
  </div>
</div>

<!-- CTA Banner -->
<div class="container cta-banner">
  <h2>Need a Similar Setup for Your Land or Project?</h2>
  <a href="/contact.php" class="btn" style="margin-top:12px;">Contact Our Team →</a>
</div>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+I%27m+exploring+Warung+Pump+projects" class="sticky-whatsapp"><span class="emoji">💬</span> Ask Us How It Works</a>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?>
  <!-- FOOTER (fallback) -->
  <div id="footer"></div>
<?php } ?>
