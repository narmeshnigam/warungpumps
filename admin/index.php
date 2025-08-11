<?php
declare(strict_types=1);
require_once __DIR__ . '/auth/guard.php';
require_once __DIR__ . '/lib/db.php';

$totProducts = (int)db()->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totDocs     = (int)db()->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$totFaqs     = (int)db()->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
$totInqs     = (int)db()->query("
  SELECT
    (SELECT COUNT(*) FROM contact_enquiries) +
    (SELECT COUNT(*) FROM contact_messages) +
    (SELECT COUNT(*) FROM dealer_inquiries) +
    (SELECT COUNT(*) FROM dealer_applications)
")->fetchColumn();

include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/sidebar.php';
include __DIR__ . '/partials/topbar.php';
?>
<main class="content">
  <h1>Dashboard</h1>
  <div class="cards">
    <div class="card"><h3>Products</h3><p><?= $totProducts ?></p></div>
    <div class="card"><h3>Downloads</h3><p><?= $totDocs ?></p></div>
    <div class="card"><h3>FAQs</h3><p><?= $totFaqs ?></p></div>
    <div class="card"><h3>Inquiries</h3><p><?= $totInqs ?></p></div>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
