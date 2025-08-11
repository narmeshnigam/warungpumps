<?php
// product-detail.php — Warung Pumps (DB-connected Product Detail)
// Fetches product by ?id=..., shows specs/features/downloads/media, and related products.
// Works with either $pdo (PDO) or $conn (MySQLi) from your config.php/db.php.

declare(strict_types=1);
mb_internal_encoding('UTF-8');

$root = __DIR__;
foreach (['config.php','db.php','config/db.php'] as $f) { $p=$root.'/'.$f; if (file_exists($p)) require_once $p; }

function db_is_pdo(): bool { return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO; }
function db_is_mysqli(): bool { return isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli; }

function db_fetch_all(string $sql, array $params=[]): array {
  if (db_is_pdo()) {
    $st = $GLOBALS['pdo']->prepare($sql);
    foreach ($params as $k=>$v) { $st->bindValue(is_int($k)?$k+1:':'.$k, $v, is_int($v)?PDO::PARAM_INT:PDO::PARAM_STR); }
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
  } elseif (db_is_mysqli()) {
    $conn = $GLOBALS['conn'];
    if ($params) {
      foreach ($params as $k=>$v) {
        $rep = is_int($v) ? (string)$v : ("'".$conn->real_escape_string((string)$v)."'");
        $sql = preg_replace('/(:'.preg_quote((string)$k,'/').')|\?/', $rep, $sql, 1);
      }
    }
    $res = $conn->query($sql);
    if (!$res) return [];
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r; return $rows;
  }
  return [];
}
function db_fetch_one(string $sql, array $params=[]): ?array {
  $rows = db_fetch_all($sql,$params);
  return $rows[0] ?? null;
}

$id = max(1, (int)($_GET['id'] ?? 0));
if (!$id) { http_response_code(404); $id = 0; }

$placeholderImg = '/assets/images/placeholder.jpg';

// --- Load main product (from products table used earlier) ---
$prod = db_fetch_one("
  SELECT id, name, slug, short_description, image_url,
         type, phase, usage_tag, hp_min, hp_max,
         COALESCE(is_active,1) AS is_active, created_at
  FROM products
  WHERE id = :id
  LIMIT 1
", ['id'=>$id]);

if (!$prod || (int)$prod['is_active'] !== 1) {
  http_response_code(404);
  $prod = [
    'id'=>0,'name'=>'Product Not Found','slug'=>'','short_description'=>'',
    'image_url'=>$placeholderImg,'type'=>'','phase'=>'','usage_tag'=>'',
    'hp_min'=>null,'hp_max'=>null
  ];
}

// --- Optional auxiliary tables (all graceful if empty) ---
$features = db_fetch_all("
  SELECT feature
  FROM product_features
  WHERE product_id = :id
  ORDER BY COALESCE(sort_order,0), id
", ['id'=>$id]);

$specs = db_fetch_all("
  SELECT param, value
  FROM product_specs
  WHERE product_id = :id
  ORDER BY COALESCE(sort_order,0), id
", ['id'=>$id]);

$downloads = db_fetch_all("
  SELECT title, file_url
  FROM product_files
  WHERE product_id = :id
  ORDER BY COALESCE(sort_order,0), id
", ['id'=>$id]);

$media = db_fetch_all("
  SELECT kind, url
  FROM product_media
  WHERE product_id = :id
  ORDER BY COALESCE(sort_order,0), id
", ['id'=>$id]);
// pick first curve/image if present
$heroImg = $prod['image_url'] ?: ($media[0]['url'] ?? $placeholderImg);
$curveImg = '';
foreach ($media as $m) { if (strtolower($m['kind']) === 'curve') { $curveImg = $m['url']; break; }}

// tags (if you store them in a table); else synthesize from existing columns
$tags = db_fetch_all("
  SELECT tag
  FROM product_tags
  WHERE product_id = :id
  ORDER BY COALESCE(sort_order,0), id
", ['id'=>$id]);
if (!$tags) {
  $tagsSynth = [];
  if (!empty($prod['usage_tag'])) $tagsSynth[] = $prod['usage_tag'];
  if (!empty($prod['type'])) $tagsSynth[] = $prod['type'];
  if (!empty($prod['phase'])) $tagsSynth[] = $prod['phase'].' Phase';
  $tags = array_map(fn($t)=>['tag'=>$t], $tagsSynth);
}

// helpers
function hp_label($min,$max): string {
  if ($min === null && $max === null) return '';
  if ($min !== null && $max !== null && (float)$min!==(float)$max) return rtrim(rtrim((string)$min,'0'),'.').'–'.rtrim(rtrim((string)$max,'0'),'.').' HP';
  $v = $min ?? $max;
  return rtrim(rtrim((string)$v,'0'),'.').' HP';
}
$hpText = hp_label($prod['hp_min'], $prod['hp_max']);

// --- Related products (prefer same type, else same usage) ---
$related = [];
if (!empty($prod['type'])) {
  $related = db_fetch_all("
    SELECT id, name, slug, image_url, hp_min, hp_max
    FROM products
    WHERE COALESCE(is_active,1)=1 AND type = :type AND id <> :id
    ORDER BY COALESCE(popularity,0) DESC, created_at DESC
    LIMIT 8
  ", ['type'=>$prod['type'], 'id'=>$prod['id']]);
}
if (!$related && !empty($prod['usage_tag'])) {
  $related = db_fetch_all("
    SELECT id, name, slug, image_url, hp_min, hp_max
    FROM products
    WHERE COALESCE(is_active,1)=1 AND usage_tag = :u AND id <> :id
    ORDER BY COALESCE(popularity,0) DESC, created_at DESC
    LIMIT 8
  ", ['u'=>$prod['usage_tag'], 'id'=>$prod['id']]);
}

$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(($prod['name'] ?: 'Product').' – Specs, Features & Pricing | Warung Pumps') ?></title>
  <meta name="description" content="<?= htmlspecialchars(($prod['short_description'] ?: 'See full technical specifications, features and use cases for this Warung Pumps model.')) ?>">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/style.main.css">
</head>
<body>

<?php if (file_exists($headerPath)) { include $headerPath; } else { ?><div id="header"></div><?php } ?>

<!-- Overview -->
<div class="container wp-pd-flex">
  <div class="wp-pd-media">
    <img src="<?= htmlspecialchars($heroImg ?: $placeholderImg) ?>"
         alt="<?= htmlspecialchars($prod['name'] ?: 'Warung Pump') ?>"
         onerror="this.onerror=null;this.src='<?= htmlspecialchars($placeholderImg) ?>';">
  </div>
  <div class="wp-pd-overview">
    <h1><?= htmlspecialchars($prod['name'] ?: 'Product') ?></h1>
    <p class="muted">
      <?php
        $bits = [];
        if ($hpText) $bits[] = $hpText;
        if (!empty($prod['usage_tag'])) $bits[] = $prod['usage_tag'];
        if (!empty($prod['type'])) $bits[] = $prod['type'];
        if (!empty($prod['phase'])) $bits[] = $prod['phase'].' Phase';
        echo htmlspecialchars(implode(' | ', $bits));
      ?>
    </p>
    <div class="spec-grid">
      <?php
        // Show a few highlight specs if present in specs table; else fall back to common fields.
        $highlights = array_slice($specs, 0, 5);
        if ($highlights) {
          foreach ($highlights as $s) {
            echo '<div><strong>'.htmlspecialchars($s['param']).':</strong> '.htmlspecialchars($s['value']).'</div>';
          }
        } else {
          if ($hpText) echo '<div><strong>Power:</strong> '.htmlspecialchars($hpText).'</div>';
          if (!empty($prod['phase'])) echo '<div><strong>Phase:</strong> '.htmlspecialchars($prod['phase']).'</div>';
          if (!empty($prod['usage_tag'])) echo '<div><strong>Usage:</strong> '.htmlspecialchars($prod['usage_tag']).'</div>';
          if (!empty($prod['type'])) echo '<div><strong>Type:</strong> '.htmlspecialchars($prod['type']).'</div>';
        }
      ?>
    </div>
    <a href="https://wa.me/918292397155?text=I'm+interested+in+<?= urlencode($prod['name'] ?: 'this Warung pump') ?>" class="btn wp-pd-enquire">Enquire Now</a>
  </div>
</div>

<!-- Tags -->
<?php if ($tags): ?>
<div class="container tag-list">
  <?php foreach ($tags as $t): if (!trim((string)$t['tag'])) continue; ?>
    <div class="tag"><?= htmlspecialchars($t['tag']) ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Features -->
<?php if ($features): ?>
<div class="container">
  <h2>Key Features</h2>
  <ul class="features">
    <?php foreach ($features as $f): if (!trim((string)$f['feature'])) continue; ?>
      <li><?= htmlspecialchars($f['feature']) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<!-- Technical Specifications -->
<?php if ($specs): ?>
<div class="container">
  <h2>Technical Specifications</h2>
  <div class="table-scroll">
    <table class="wp-pd-table">
      <thead><tr><th>Parameter</th><th>Value</th></tr></thead>
      <tbody>
        <?php foreach ($specs as $s): ?>
          <tr><td><?= htmlspecialchars($s['param']) ?></td><td><?= htmlspecialchars($s['value']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Performance Curve -->
<?php if ($curveImg): ?>
<div class="container">
  <h2>Performance Curve</h2>
  <img class="wp-pd-curve" src="<?= htmlspecialchars($curveImg) ?>"
       alt="<?= htmlspecialchars(($prod['name'] ?: 'Pump').' performance chart') ?>"
       onerror="this.style.display='none'">
  <p class="note">Performance chart shows typical head vs. flow for this model.</p>
</div>
<?php endif; ?>

<!-- Downloads -->
<?php if ($downloads): ?>
<div class="container">
  <h2>Downloads</h2>
  <div class="download-links">
    <?php foreach ($downloads as $d):
      $title = $d['title'] ?: 'Download';
      $url   = $d['file_url'] ?: '#';
    ?>
      <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($title) ?></a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Inquiry (static form markup; you can wire to handler later) -->
<div class="container">
  <div class="inquiry-box">
    <h2>Need Help With This Model?</h2>
    <form method="post" action="/contact.php">
      <input type="hidden" name="product" value="<?= htmlspecialchars($prod['name'] ?: '') ?>">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="tel" name="phone" placeholder="Phone Number" required>
      <input type="text" name="location" placeholder="Your Location">
      <textarea name="query" rows="4" placeholder="Tell us your requirement..."></textarea>
      <button type="submit" class="btn">Submit Inquiry</button>
    </form>
  </div>
</div>

<!-- Related Products -->
<?php if ($related): ?>
<div class="container">
  <h2>Related Products</h2>
  <div class="product-grid">
    <?php foreach ($related as $r):
      $img = $r['image_url'] ?: $placeholderImg;
      $hp  = hp_label($r['hp_min'], $r['hp_max']);
      $url = '/product-detail.php?id='.(int)$r['id'];
    ?>
    <article class="card product-card">
      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['name']) ?>"
           loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($placeholderImg) ?>';">
      <h3><?= htmlspecialchars($r['name']) ?></h3>
      <?php if ($hp): ?><p class="pill"><?= htmlspecialchars($hp) ?></p><?php endif; ?>
      <a href="<?= htmlspecialchars($url) ?>" class="btn">View Details</a>
    </article>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?><div id="footer"></div><?php } ?>

<script src="/load-assets.js"></script>
</body>
</html>
