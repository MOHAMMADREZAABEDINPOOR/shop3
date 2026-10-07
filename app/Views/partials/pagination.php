<?php
/** @var int $page @var int $pages */
if (($pages ?? 1) <= 1) return;
$window = 2;
$start = max(1, $page - $window);
$end   = min($pages, $page + $window);
?>
<nav class="pagination" aria-label="صفحه‌بندی">
    <?php if ($page > 1): ?>
        <a href="<?= e(url_with(['page' => $page - 1])) ?>" class="page-btn" aria-label="قبلی"><?= icon('chevron-right', 16) ?></a>
    <?php endif; ?>

    <?php if ($start > 1): ?>
        <a href="<?= e(url_with(['page' => 1])) ?>" class="page-btn"><?= l_num(1) ?></a>
        <?php if ($start > 2): ?><span class="page-dots">…</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= e(url_with(['page' => $i])) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= l_num($i) ?></a>
    <?php endfor; ?>

    <?php if ($end < $pages): ?>
        <?php if ($end < $pages - 1): ?><span class="page-dots">…</span><?php endif; ?>
        <a href="<?= e(url_with(['page' => $pages])) ?>" class="page-btn"><?= l_num($pages) ?></a>
    <?php endif; ?>

    <?php if ($page < $pages): ?>
        <a href="<?= e(url_with(['page' => $page + 1])) ?>" class="page-btn" aria-label="بعدی"><?= icon('chevron-left', 16) ?></a>
    <?php endif; ?>
</nav>
