<?php /** @var array $items — هر آیتم: ['label' => ..., 'url' => ...] */ ?>
<nav class="breadcrumb" aria-label="مسیر صفحه">
    <a href="/"><?= icon('home', 14) ?> خانه</a>
    <?php foreach ($items as $item): ?>
        <span class="sep"><?= icon('chevron-left', 12) ?></span>
        <?php if (!empty($item['url'])): ?>
            <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
        <?php else: ?>
            <span class="current"><?= e($item['label']) ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
