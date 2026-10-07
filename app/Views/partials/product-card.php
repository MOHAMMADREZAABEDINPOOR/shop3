<?php
/** @var array $product */
$p = $product;
$final = final_price($p);
$percent = discount_percent($p['price'], $p['discount_price']);
$outOfStock = (int)$p['stock'] <= 0;
?>
<article class="product-card <?= $outOfStock ? 'out-of-stock' : '' ?>">
    <a href="/product/<?= e($p['slug']) ?>" class="product-image" aria-label="<?= e(product_name($p)) ?>">
        <img src="<?= e(product_image($p['image'])) ?>" alt="<?= e(product_name($p)) ?>" loading="lazy">
        <?php if ($percent > 0): ?>
            <span class="badge-discount"><?= !I18n::isRtl() ? ($percent . '% OFF') : ('٪' . fa_num($percent) . ' تخفیف') ?></span>
        <?php endif; ?>
        <?php if ($outOfStock): ?>
            <span class="badge-out"><?= __('out_of_stock', 'ناموجود') ?></span>
        <?php endif; ?>
        <div class="product-actions">
            <button class="icon-btn wishlist-btn" data-product="<?= (int)$p['id'] ?>" aria-label="<?= __('wishlist', 'علاقه‌مندی') ?>" title="<?= __('wishlist', 'علاقه‌مندی') ?>"><?= icon('heart', 18) ?></button>
            <a href="/product/<?= e($p['slug']) ?>" class="icon-btn" aria-label="<?= e(product_name($p)) ?>" title="<?= e(product_name($p)) ?>"><?= icon('eye', 18) ?></a>
        </div>
    </a>
    <div class="product-info">
        <div class="product-meta">
            <span class="product-brand"><?= e($p['brand'] ?? '') ?></span>
            <?= stars((float)$p['rating_avg']) ?>
        </div>
        <h3 class="product-name"><a href="/product/<?= e($p['slug']) ?>"><?= e(product_name($p)) ?></a></h3>
        <div class="product-price-row">
            <div class="product-price">
                <?php if ($percent > 0): ?>
                    <del><?= price($p['price'], false) ?></del>
                <?php endif; ?>
                <strong><?= price($final) ?></strong>
            </div>
            <?php if (!$outOfStock): ?>
                <button class="btn btn-primary btn-icon add-to-cart" data-product="<?= (int)$p['id'] ?>" aria-label="<?= __('add_to_cart') ?>" title="<?= __('add_to_cart') ?>">
                    <?= icon('cart', 18) ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</article>
