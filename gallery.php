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
$placeholder   = '/assets/images/placeholder.jpg';
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
$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pump Installation Gallery & Projects | Warung Pumps</title>
  <meta name="description" content="Explore real-world installation photos, case studies, and project examples of Warung Pumps in action — from farmlands to industries.">
  <!-- keep your existing CSS link -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <!-- optional: your main bundle -->
  <link rel="stylesheet" href="/assets/css/style.main.css">
  <style>
    /* Page-local safety (you can remove after copying into style.main.css) */
    :root { --blue:#0A3D62; --orange:#F76C1C; --highlight:#E8F0FE; --text:#4A4A4A; --bg:#F9F9F9; }
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Open Sans',sans-serif; background:var(--bg); color:var(--text); line-height:1.6; }
    h1,h2,h3 { font-family:'Poppins',sans-serif; color:var(--blue); }
    h1 { font-size:36px; margin-bottom:12px; }
    h2 { font-size:28px; margin-bottom:12px; }
    .container { max-width:1280px; margin:auto; padding:48px 24px; }
    .btn { background:var(--orange); color:#fff; padding:10px 20px; border-radius:10px; font-family:'Montserrat',sans-serif; font-weight:600; text-decoration:none; display:inline-block; transition:0.3s ease; }
    .btn:hover { filter:brightness(110%); transform:scale(1.05); }
    .hero { background:url('/assets/images/gallery-banner.jpg') center/cover no-repeat; height:60vh; display:flex; align-items:center; justify-content:flex-start; padding-left:10%; position:relative; color:#fff; }
    .hero::after { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.5); z-index:1; }
    .hero-content { position:relative; z-index:2; max-width:600px; }
    .filter-bar { display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-top:24px; }
    .filter-tag { background:#fff; padding:8px 16px; border-radius:20px; border:1px solid var(--blue); color:var(--blue); cursor:pointer; font-weight:600; text-decoration:none; }
    .filter-tag.active, .filter-tag:hover { background:var(--highlight); }
    .grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(260px,1fr)); gap:16px; margin-top:32px; }
    .gallery-img { border-radius:14px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.08); transition:transform 0.3s ease; cursor:pointer; background:#fff; }
    .gallery-img img { width:100%; display:block; height:auto; transition:transform 0.3s ease; }
    .gallery-img:hover img { transform:scale(1.05); }
    .project { display:flex; flex-wrap:wrap; gap:24px; margin-top:48px; background:#fff; border-radius:16px; box-shadow:0 4px 16px rgba(0,0,0,0.08); overflow:hidden; }
    .project img { width:100%; max-width:480px; height:auto; object-fit:cover; }
    .project-content { padding:24px; flex:1; }
    .video-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:24px; margin-top:32px; }
    iframe { width:100%; height:240px; border:none; border-radius:14px; }
    .cta-banner { background:var(--highlight); padding:48px 24px; text-align:center; border-radius:14px; margin-top:64px; }
    .sticky-whatsapp { position:fixed; bottom:16px; right:16px; background:#25D366; color:#fff; padding:12px 20px; border-radius:50px; font-weight:bold; text-decoration:none; box-shadow:0 4px 12px rgba(0,0,0,0.2); z-index:999; }
    .pagination { display:flex; gap:8px; justify-content:center; margin:32px 0; flex-wrap:wrap; }
    .page-btn { padding:8px 14px; border-radius:10px; border:1px solid var(--blue); color:var(--blue); text-decoration:none; font-weight:600; }
    .page-btn.active, .page-btn:hover { background:var(--orange); color:#fff; border-color:var(--orange); }
  </style>
</head>
<body>

<?php if (file_exists($headerPath)) { include $headerPath; } else { ?>
  <!-- HEADER (fallback to your old loader structure) -->
  <div id="header"></div>
<?php } ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1>See Our Pumps in Action</h1>
    <p>Installations, projects, and proud partners across India.</p>
  </div>
</section>

<div class="container">
  <!-- Filter Tags -->
  <div class="filter-bar">
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
    <img src="/assets/gallery/project-etawah.jpg" alt="Etawah Submersible">
    <div class="project-content">
      <h3>Submersible Setup for 5 Acre Field – Etawah</h3>
      <p><strong>Pump Used:</strong> WRG-3HP-SUB</p>
      <p><strong>Outcome:</strong> Reduced watering time by 30%, full 2-acre spray range</p>
      <a href="/product-detail.php" class="btn">View Product →</a>
    </div>
  </div>
  <div class="project">
    <img src="/assets/gallery/project-kanpur.jpg" alt="Kanpur Tank">
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

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?>
  <!-- FOOTER (fallback) -->
  <div id="footer"></div>
<?php } ?>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+I%27m+exploring+Warung+Pump+projects" class="sticky-whatsapp"><span class="emoji">💬</span> Ask Us How It Works</a>

<!-- keep your original asset loader if you still use header/footer via JS -->
<script src="/load-assets.js"></script>
</body>
</html>
