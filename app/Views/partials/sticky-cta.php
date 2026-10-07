<?php
/** @var array $product */
$final = final_price($product);
$percent = discount_percent($product['price'], $product['discount_price']);
$out = (int)$product['stock'] <= 0;
if ($out) return;
?>
<div class="sticky-cta" id="stickyCta">
    <div class="sc-price">
        <?php if ($percent > 0): ?><del><?= price($product['price'], false) ?></del><?php endif; ?>
        <strong><?= price($final) ?></strong>
    </div>
    <button class="btn btn-primary add-to-cart" data-product="<?= (int)$product['id'] ?>"><?= icon('cart', 18) ?> افزودن به سبد</button>
</div>
