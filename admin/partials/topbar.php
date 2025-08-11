<?php declare(strict_types=1); ?>
<header class="topbar">
  <div class="title"><?= e(BRAND_NAME) ?></div>
  <div class="actions">
    <span class="hello">Hi, <?= e($_SESSION['admin']['name'] ?? 'Admin') ?></span>
    <a class="btn btn-small" href="<?= e(BASE_URL) ?>/logout.php">Logout</a>
  </div>
</header>
