<?php
// selector-tool.php — Warung Pumps
declare(strict_types=1);
mb_internal_encoding('UTF-8');

$root = __DIR__;
foreach (['config.php','db.php','config/db.php'] as $f) { $p=$root.'/'.$f; if (file_exists($p)) require_once $p; }

function db_is_pdo(): bool { return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO; }
function db_is_mysqli(): bool { return isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli; }

function db_fetch_all(string $sql, array $params=[]): array {
  if (db_is_pdo()) {
    $st = $GLOBALS['pdo']->prepare($sql);
    $i = 1;
    foreach ($params as $k=>$v) {
      $type = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $st->bindValue(is_int($k)?$i++:(':'.$k), $v, $type);
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

$placeholderImg = '/assets/images/placeholder.jpg';

/**
 * INPUTS
 */
$usage = isset($_POST['usage']) ? trim((string)$_POST['usage']) : '';
$source = isset($_POST['source']) ? trim((string)$_POST['source']) : '';
$depth = isset($_POST['depth']) ? (int)$_POST['depth'] : 0;
$phase = isset($_POST['phase']) ? trim((string)$_POST['phase']) : '';

/**
 * Heuristics
 * - Map source -> preferred product type(s)
 * - Map depth -> suggested HP band
 */
$preferredTypes = [];
switch (strtolower($source)) {
  case 'borewell':              $preferredTypes = ['Submersible','Tubewell']; break;
  case 'open well':             $preferredTypes = ['Openwell']; break;
  case 'overhead tank':         $preferredTypes = ['Openwell']; break; // sometimes boosters; keep Openwell in catalog
  case 'underground reservoir': $preferredTypes = ['Submersible','Openwell']; break;
  default:                      $preferredTypes = []; // no restriction
}

$hpMin = null; $hpMax = null; // depth->hp band (very rough guide)
if ($depth > 0) {
  if ($depth <= 80)      { $hpMin = 0.5; $hpMax = 1.5; }
  elseif ($depth <= 180) { $hpMin = 1.5; $hpMax = 3.0; }
  elseif ($depth <= 280) { $hpMin = 3.0; $hpMax = 5.0; }
  else                   { $hpMin = 5.0; $hpMax = null; }
}

/**
 * Build query for recommendations
 */
$params = [];
$sql = "SELECT id, name, image_url, type, phase, usage_tag, hp_min, hp_max, short_description
        FROM products
        WHERE COALESCE(is_active,1)=1";

if ($usage !== '') {
  $sql .= " AND usage_tag = :usage";
  $params['usage'] = $usage;
}
if ($phase === 'Single' || $phase === 'Three') {
  $sql .= " AND phase = :phase";
  $params['phase'] = $phase;
}
if ($preferredTypes) {
  // IN clause
  $in = [];
  foreach ($preferredTypes as $i=>$t) {
    $key = "t$i"; $in[] = ":$key"; $params[$key] = $t;
  }
  $sql .= " AND type IN (".implode(',', $in).")";
}
if ($hpMin !== null && $hpMax !== null) {
  $sql .= " AND ((hp_min IS NULL AND hp_max IS NULL) OR (hp_min <= :hpMax AND hp_max >= :hpMin))";
  $params['hpMin'] = $hpMin;
  $params['hpMax'] = $hpMax;
} elseif ($hpMin !== null && $hpMax === null) {
  $sql .= " AND ((hp_min IS NULL AND hp_max IS NULL) OR (hp_max >= :hpMin))";
  $params['hpMin'] = $hpMin;
}

$sql .= " ORDER BY COALESCE(popularity,0) DESC, created_at DESC LIMIT :limit";

$recommendations = ($usage || $source || $depth || $phase) ? db_fetch_all($sql, array_merge($params,['limit'=>12])) : [];

/** helpers */
function hp_label($min,$max): string {
  if ($min === null && $max === null) return '';
  if ($min !== null && $max !== null && (float)$min!==(float)$max)
    return rtrim(rtrim((string)$min,'0'),'.').'–'.rtrim(rtrim((string)$max,'0'),'.').' HP';
  $v = $min ?? $max;
  return rtrim(rtrim((string)$v,'0'),'.').' HP';
}

$page_title = 'Pump Selector Tool – Find the Right Pump in Minutes | Warung Pumps';
$page_description = 'Use the Warung Pump Selector Tool to get a personalized recommendation by depth, usage, and power.';
$headerPath = $root.'/includes/header.php';
$footerPath = $root.'/includes/footer.php';
?>
<?php if (file_exists($headerPath)) { include $headerPath; } else { ?><div id="header"></div><?php } ?>

<!-- Hero -->
<section class="selector-hero">
  <div class="container center">
    <h1>Find the Right Pump in Just 4 Steps</h1>
    <p>Use our smart selector tool to get a personalized pump recommendation.</p>
    <a href="#step1" class="btn mt-16">Start Now →</a>
  </div>
</section>

<div class="container">
  <div class="progress">Step <span id="stepNow">1</span> of 4</div>

  <form method="post" id="selectorForm">
    <!-- Step 1 -->
    <div id="step1" class="form-step active">
      <h3>What will the pump be used for?</h3>
      <?php
        // Static choices aligned to products. You can lift distinct usage_tag from DB if you prefer.
        $usageOptions = ['Agricultural','Domestic','Commercial','Government Project'];
        foreach ($usageOptions as $opt) {
          $checked = ($usage===$opt) ? ' checked' : '';
          echo '<label><input type="radio" name="usage" value="'.htmlspecialchars($opt).'"'.$checked.'> '.$opt.'</label>';
        }
      ?>
      <div class="step-buttons">
        <span></span>
        <a href="#step2" class="btn next">Next →</a>
      </div>
    </div>

    <!-- Step 2 -->
    <div id="step2" class="form-step">
      <h3>What is your water source type?</h3>
      <?php
        $sourceOptions = ['Borewell','Open Well','Overhead Tank','Underground Reservoir'];
        foreach ($sourceOptions as $opt) {
          $checked = ($source===$opt) ? ' checked' : '';
          echo '<label><input type="radio" name="source" value="'.htmlspecialchars($opt).'"'.$checked.'> '.$opt.'</label>';
        }
      ?>
      <div class="step-buttons">
        <a href="#step1" class="btn back">← Back</a>
        <a href="#step3" class="btn next">Next →</a>
      </div>
    </div>

    <!-- Step 3 -->
    <div id="step3" class="form-step">
      <h3>Approximate water depth (in feet)?</h3>
      <input type="range" name="depth" id="depth" min="20" max="500" value="<?= $depth ?: 150 ?>">
      <div class="depth-readout">Depth: <strong id="depthVal"><?= $depth ?: 150 ?></strong> ft</div>
      <div class="step-buttons">
        <a href="#step2" class="btn back">← Back</a>
        <a href="#step4" class="btn next">Next →</a>
      </div>
    </div>

    <!-- Step 4 -->
    <div id="step4" class="form-step">
      <h3>What kind of power supply do you have?</h3>
      <?php
        $phaseOptions = ['Single','Three'];
        foreach ($phaseOptions as $opt) {
          $checked = ($phase===$opt) ? ' checked' : '';
          echo '<label><input type="radio" name="phase" value="'.htmlspecialchars($opt).'"'.$checked.'> '.$opt.' Phase</label>';
        }
      ?>
      <div class="step-buttons">
        <a href="#step3" class="btn back">← Back</a>
        <button type="submit" class="btn">Show Results</button>
      </div>
    </div>
  </form>
</div>

<!-- Results -->
<div class="container recommendation" id="result">
  <h2>Recommended Pump Models</h2>

  <?php if ($recommendations): ?>
    <div class="result-grid">
      <?php foreach ($recommendations as $p):
        $img = $p['image_url'] ?: $placeholderImg;
        $hp  = hp_label($p['hp_min'], $p['hp_max']);
        $desc = $p['short_description'] ?: ($p['type'].' | '.$p['usage_tag']);
      ?>
      <article class="card">
        <h4><?= htmlspecialchars($p['name']) ?></h4>
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
             loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($placeholderImg) ?>';" style="width:100%;border-radius:10px;margin:8px 0;">
        <?php if ($hp): ?><p><strong>Power:</strong> <?= htmlspecialchars($hp) ?></p><?php endif; ?>
        <?php if (!empty($p['phase'])): ?><p><strong>Phase:</strong> <?= htmlspecialchars($p['phase']) ?></p><?php endif; ?>
        <?php if (!empty($p['type'])): ?><p><strong>Type:</strong> <?= htmlspecialchars($p['type']) ?></p><?php endif; ?>
        <p><?= htmlspecialchars($desc) ?></p>
        <a href="/product-detail.php?id=<?= (int)$p['id'] ?>" class="btn mt-12">View Specs</a>
        <a href="https://wa.me/918292397155?text=I'm+interested+in+<?= urlencode($p['name']) ?>" class="btn alt mt-12">Enquire</a>
      </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted">Fill the steps above and hit “Show Results” to see recommended models. If you don’t get results, try changing the source or depth a bit.</p>
  <?php endif; ?>
</div>

<!-- Support CTA -->
<div class="container support-box">
  <div>
    <h2>Still Unsure Which Pump Fits Best?</h2>
    <p>Our team will guide you personally. Share your depth, usage, and budget.</p>
  </div>
  <div>
    <a href="https://wa.me/918292397155" class="btn">Chat on WhatsApp</a>
  </div>
</div>

<a href="https://wa.me/918292397155?text=Hi%2C+I%27d+like+help+with+pump+selection" class="sticky-whatsapp"><span class="emoji">💬</span> Talk to an Expert</a>

<script>
  // Lightweight stepper & depth readout
  const steps = Array.from(document.querySelectorAll('.form-step'));
  const depth = document.getElementById('depth');
  const depthVal = document.getElementById('depthVal');
  const stepNow = document.getElementById('stepNow');

  function showStep(id) {
    steps.forEach(s => s.classList.remove('active'));
    const el = document.querySelector(id);
    if (el) el.classList.add('active');
    const n = ['#step1','#step2','#step3','#step4'].indexOf(id) + 1;
    if (n>0) stepNow.textContent = n;
  }
  window.addEventListener('hashchange', () => showStep(location.hash || '#step1'));
  document.querySelectorAll('.next').forEach(btn => btn.addEventListener('click', e => {
    // prevent jump if no selection in current step
    const parent = e.target.closest('.form-step');
    const requiredRadios = parent.querySelectorAll('input[type=radio]');
    if (requiredRadios.length) {
      const anyChecked = Array.from(requiredRadios).some(r => r.checked);
      if (!anyChecked) { e.preventDefault(); alert('Please select an option to continue.'); }
    }
  }));
  document.querySelectorAll('.back').forEach(btn => btn.addEventListener('click', () => {}));
  if (depth && depthVal) depth.addEventListener('input', () => depthVal.textContent = depth.value);
  showStep(location.hash || '#step1');
</script>

<?php if (file_exists($footerPath)) { include $footerPath; } else { ?><div id="footer"></div><?php } ?>
