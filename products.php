<?php
// products.php — Warung Pumps (DB-connected Products Listing)
// Assumes you already load DB via config.php/db.php. Works with $pdo (PDO) or $conn (MySQLi).

declare(strict_types=1);
mb_internal_encoding('UTF-8');

$root = __DIR__;
foreach (['config.php','db.php','config/db.php'] as $f) { $p=$root.'/'.$f; if (file_exists($p)) require_once $p; }

function db_is_pdo(): bool { return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO; }
function db_is_mysqli(): bool { return isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli; }

function db_fetch_all(string $sql, array $params=[]): array {
  if (db_is_pdo()) {
    $st = $GLOBALS['pdo']->prepare($sql);
    foreach ($params as $k=>$v) {
      $st->bindValue(is_int($k)?$k+1:':'.$k, $v, is_int($v)?PDO::PARAM_INT:PDO::PARAM_STR);
    }
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
function db_fetch_value(string $sql, array $params=[]){
  $rows=db_fetch_all($sql,$params); if(!$rows) return null; $row=array_values($rows[0]); return $row[0]??null;
}

// --- CONFIG: map to your column names if different ---
$TBL_PRODUCTS   = 'products';
$TBL_CATEGORIES = 'product_categories'; // optional; used for "type" list if present
$COL_ID         = 'id';
$COL_NAME       = 'name';
$COL_SLUG       = 'slug';
$COL_DESC       = 'short_description';   // fallback handled below
$COL_IMAGE      = 'image_url';
$COL_TYPE       = 'type';                // e.g., 'Tubewell' | 'Submersible' | 'Openwell'
$COL_PHASE      = 'phase';               // 'Single' | 'Three'
$COL_HP_MIN     = 'hp_min';              // numeric
$COL_HP_MAX     = 'hp_max';              // numeric
$COL_USAGE      = 'usage_tag';           // 'Agricultural' | 'Domestic' | 'Commercial'
$COL_POP        = 'popularity';          // numeric rank
$COL_ACTIVE     = 'is_active';
$COL_CREATED    = 'created_at';

// --- Inputs ---
$selType   = trim((string)($_GET['type']  ?? ''));
$selPhase  = trim((string)($_GET['phase'] ?? ''));
$selHp     = trim((string)($_GET['hp']    ?? ''));
$selUsage  = trim((string)($_GET['use']   ?? ''));
$selSort   = trim((string)($_GET['sort']  ?? ''));
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 12;
$offset    = ($page-1)*$perPage;

$placeholderImg = '/assets/images/placeholder.jpg';

// --- Filter options (pull distincts from DB, with graceful fallbacks) ---
$types  = db_fetch_all("SELECT DISTINCT $COL_TYPE AS v FROM $TBL_PRODUCTS WHERE COALESCE($COL_ACTIVE,1)=1 AND $COL_TYPE IS NOT NULL AND $COL_TYPE<>'' ORDER BY v");
$phases = db_fetch_all("SELECT DISTINCT $COL_PHASE AS v FROM $TBL_PRODUCTS WHERE COALESCE($COL_ACTIVE,1)=1 AND $COL_PHASE IS NOT NULL AND $COL_PHASE<>'' ORDER BY v");
$usages = db_fetch_all("SELECT DISTINCT $COL_USAGE AS v FROM $TBL_PRODUCTS WHERE COALESCE($COL_ACTIVE,1)=1 AND $COL_USAGE IS NOT NULL AND $COL_USAGE<>'' ORDER BY v");

// HP ranges (static buckets to keep UX tidy)
$hpBuckets = [
  '0.5-1' => [0.5, 1.0],
  '1.5-3' => [1.5, 3.0],
  '3-5'   => [3.0, 5.0],
  '5+'    => [5.0, 999.0],
];

// --- Build WHERE ---
$where = ["COALESCE($COL_ACTIVE,1)=1"];
$params = [];

if ($selType !== '')  { $where[]="$COL_TYPE = :type";   $params['type']=$selType; }
if ($selPhase !== '') { $where[]="$COL_PHASE = :phase"; $params['phase']=$selPhase; }
if ($selUsage !== '') { $where[]="$COL_USAGE = :usage"; $params['usage']=$selUsage; }
if ($selHp !== '' && isset($hpBuckets[$selHp])) {
  [$lo,$hi] = $hpBuckets[$selHp];
  // Overlap condition: any product whose [hp_min,hp_max] intersects selected bucket
  $where[] = "(COALESCE($COL_HP_MIN,0) <= :hi AND COALESCE($COL_HP_MAX,COALESCE($COL_HP_MIN,0)) >= :lo)";
  $params['hi'] = (float)$hi;
  $params['lo'] = (float)$lo;
}
$whereSql = $where ? ('WHERE '.implode(' AND ',$where)) : '';

// --- Sorting ---
$sortSql = "ORDER BY COALESCE($COL_POP,0) DESC, $COL_CREATED DESC";
if ($selSort === 'hp_desc') $sortSql = "ORDER BY COALESCE($COL_HP_MAX, COALESCE($COL_HP_MIN,0)) DESC";
if ($selSort === 'hp_asc')  $sortSql = "ORDER BY COALESCE($COL_HP_MIN, COALESCE($COL_HP_MAX,0)) ASC";
if ($selSort === 'name')    $sortSql = "ORDER BY $COL_NAME ASC";

// --- Count ---
$total = (int)(db_fetch_value("SELECT COUNT(*) FROM $TBL_PRODUCTS $whereSql", $params) ?? 0);
$pages = max(1, (int)ceil($total/$perPage));

// --- Fetch page ---
$sql = "SELECT $COL_ID AS id, $COL_NAME AS name, $COL_SLUG AS slug,
               COALESCE($COL_DESC,'') AS description,
               COALESCE($COL_IMAGE,'') AS image_url,
               COALESCE($COL_TYPE,'') AS type,
               COALESCE($COL_PHASE,'') AS phase,
               COALESCE($COL_USAGE,'') AS usage_tag,
               COALESCE($COL_HP_MIN,NULL) AS hp_min,
               COALESCE($COL_HP_MAX,NULL) AS hp_max
        FROM $TBL_PRODUCTS
        $whereSql
        $sortSql
        LIMIT :limit OFFSET :offset";
$params['limit']=$perPage; $params['offset']=$offset;
$rows = db_fetch_all($sql, $params);

function hp_label($min,$max): string {
  if ($min === null && $max === null) return '';
  if ($min !== null && $max !== null && (float)$min !== (float)$max) return rtrim(rtrim((string)$min,'0'),'.') . '–' . rtrim(rtrim((string)$max,'0'),'.') . ' HP';
  $v = $min ?? $max;
  return rtrim(rtrim((string)$v,'0'),'.') . ' HP';
}
function qp(array $add=[], array $remove=[]): string {
  $q = $_GET ?? [];
  foreach ($remove as $r) unset($q[$r]);
  foreach ($add as $k=>$v) $q[$k]=$v;
  $s = http_build_query($q);
  return $s ? '?'.$s : '';
}

// Includes
$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Submersible, Tubewell & Openwell Pumps | Warung Pumps</title>
  <meta name="description" content="Explore a full range of pumps at Warung — including submersible, tubewell, and openwell models. Filter by usage, HP, and specs. Trusted by farmers & professionals.">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/style.main.css">
</head>
<body>

<?php if (file_exists($headerPath)) { include $headerPath; } else { ?><div id="header"></div><?php } ?>

<!-- Hero -->
<section class="wp-products-hero">
  <div class="hero-content">
    <h1>Explore Our Pump Range</h1>
    <p>Reliable models for agriculture, household, and commercial water solutions.</p>
    <a href="/selector-tool.php" class="btn">Use Pump Selector Tool</a>
  </div>
</section>

<!-- Filters -->
<div class="filter-bar">
  <form class="filter-form" method="get">
    <select name="type">
      <option value="">All Pump Types</option>
      <?php foreach ($types as $t): $v=$t['v']; if(!$v) continue; ?>
        <option value="<?= htmlspecialchars($v) ?>" <?= $selType===$v?'selected':'' ?>><?= htmlspecialchars($v) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="phase">
      <option value="">Phase</option>
      <?php foreach ($phases as $p): $v=$p['v']; if(!$v) continue; ?>
        <option value="<?= htmlspecialchars($v) ?>" <?= $selPhase===$v?'selected':'' ?>><?= htmlspecialchars($v) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="hp">
      <option value="">HP Range</option>
      <?php foreach ($hpBuckets as $k=>$rng): ?>
        <option value="<?= $k ?>" <?= $selHp===$k?'selected':'' ?>><?= $k==='5+'?'5+ HP':str_replace('-', '–', $k).' HP' ?></option>
      <?php endforeach; ?>
    </select>

    <select name="use">
      <option value="">Usage</option>
      <?php foreach ($usages as $u): $v=$u['v']; if(!$v) continue; ?>
        <option value="<?= htmlspecialchars($v) ?>" <?= $selUsage===$v?'selected':'' ?>><?= htmlspecialchars($v) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="sort">
      <option value="">Sort By</option>
      <option value="pop"     <?= $selSort===''||$selSort==='pop'?'selected':'' ?>>Popularity</option>
      <option value="hp_desc" <?= $selSort==='hp_desc'?'selected':'' ?>>HP High → Low</option>
      <option value="hp_asc"  <?= $selSort==='hp_asc'?'selected':'' ?>>HP Low → High</option>
      <option value="name"    <?= $selSort==='name'?'selected':'' ?>>Name A–Z</option>
    </select>

    <button class="btn" type="submit">Apply</button>
    <?php if (!empty($_GET)): ?>
      <a class="btn btn-outline" href="<?= htmlspecialchars(qp([], ['type','phase','hp','use','sort','page'])) ?>">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Product Grid -->
<div class="container">
  <div class="product-grid">
    <?php if (!$rows): ?>
      <p class="muted text-center" style="grid-column:1/-1;">No products match the selected filters yet.</p>
    <?php else: foreach ($rows as $r):
      $img = $r['image_url'] ?: $placeholderImg;
      $hp  = hp_label($r['hp_min'], $r['hp_max']);
      $slug = $r['slug'] ?: $r['id'];
      $detailUrl = '/product-detail.php?slug='.urlencode((string)$slug);
    ?>
      <article class="card product-card">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['name'] ?: 'Warung Pump') ?>"
             loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($placeholderImg) ?>';">
        <h3><?= htmlspecialchars($r['name'] ?: 'Untitled Pump') ?></h3>
        <?php if ($hp): ?><p class="pill"><?= htmlspecialchars($hp) ?></p><?php endif; ?>
        <?php if (!empty($r['type'])): ?><p class="meta"><strong>Type:</strong> <?= htmlspecialchars($r['type']) ?></p><?php endif; ?>
        <?php if (!empty($r['phase'])): ?><p class="meta"><strong>Phase:</strong> <?= htmlspecialchars($r['phase']) ?></p><?php endif; ?>
        <?php if (!empty($r['usage_tag'])): ?><p class="meta"><strong>Usage:</strong> <?= htmlspecialchars($r['usage_tag']) ?></p><?php endif; ?>
        <?php if (!empty($r['description'])): ?><p class="desc"><?= htmlspecialchars($r['description']) ?></p><?php endif; ?>
        <a href="<?= htmlspecialchars($detailUrl) ?>" class="btn" aria-label="View <?= htmlspecialchars($r['name'] ?: 'pump') ?>">View Details</a>
      </article>
    <?php endforeach; endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Product pages">
      <?php
        if ($page > 1) echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$page-1])).'">« Prev</a>';
        $win=3; $start=max(1,$page-$win); $end=min($pages,$page+$win);
        if ($start>1) { echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>1])).'">1</a>'; if($start>2) echo '<span class="page-ellipsis">…</span>'; }
        for($p=$start;$p<=$end;$p++){ $cls='page-btn'.($p===$page?' active':''); echo '<a class="'.$cls.'" href="'.htmlspecialchars(qp(['page'=>$p])).'">'.$p.'</a>'; }
        if ($end<$pages) { if($end<$pages-1) echo '<span class="page-ellipsis">…</span>'; echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$pages])).'">'.$pages.'</a>'; }
        if ($page < $pages) echo '<a class="page-btn" href="'.htmlspecialchars(qp(['page'=>$page+1])).'">Next »</a>';
      ?>
    </nav>
  <?php endif; ?>
</div>

<!-- Inquiry strip -->
<div class="container">
  <div class="inquiry-strip">
    <h2>Need Help Choosing the Right Model?</h2>
    <a href="https://wa.me/918292397155?text=Hi%2C+I+need+help+choosing+a+pump" class="btn">Chat with Our Expert →</a>
  </div>
</div>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?><div id="footer"></div><?php } ?>

<!-- Sticky WhatsApp -->
<a href="https://wa.me/918292397155?text=Hi%2C+I%27m+looking+for+a+pump+recommendation" target="_blank" class="sticky-whatsapp">
  <span class="emoji">💬</span> Need Help Choosing?
</a>

<script src="/load-assets.js"></script>
</body>
</html>
