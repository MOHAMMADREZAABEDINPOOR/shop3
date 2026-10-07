<?php
use App\Core\I18n;

$final = final_price($product);
$percent = discount_percent($product['price'], $product['discount_price']);
$outOfStock = (int)$product['stock'] <= 0;
$lowStock = !$outOfStock && (int)$product['stock'] <= 5;
$deliveryDate = localized_date(strtotime('+2 days'));

$prodName = product_name($product);
$prodShort = product_short($product);
$prodDesc = product_desc($product);
$prodSpecs = product_specs($product);
$catName = category_name($product);
?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [
        ['label' => __('home', 'خانه'), 'url' => '/'],
        ['label' => __('categories', 'دسته‌بندی‌ها'), 'url' => '/shop'],
        ['label' => $catName, 'url' => '/category/' . $product['category_slug']],
        ['label' => $prodName],
    ]]); ?>

    <div class="product-detail card reveal in">
        <?php
        $galleryList = !empty($product['gallery']) ? (json_decode($product['gallery'], true) ?: []) : [];
        if (empty($galleryList) && !empty($product['image'])) {
            $galleryList = [$product['image']];
        }
        ?>
        <div class="product-gallery">
            <div class="gallery-main" id="galleryMain">
                <img src="<?= e(product_image($product['image'])) ?>" alt="<?= e($prodName) ?>" id="mainImage" fetchpriority="high">
                <?php if ($percent > 0): ?>
                    <span class="badge-discount big"><?= !I18n::isRtl() ? ($percent . '% ' . __('off', 'OFF')) : ('٪' . fa_num($percent) . ' تخفیف') ?></span>
                <?php endif; ?>
            </div>
            <?php if (count($galleryList) > 1): ?>
                <div class="gallery-thumbs" id="galleryThumbs">
                    <?php foreach ($galleryList as $gIdx => $gImg): ?>
                        <button type="button" class="gallery-thumb-btn <?= $gImg === $product['image'] || ($gIdx === 0 && empty($product['image'])) ? 'active' : '' ?>" data-src="<?= e(product_image($gImg)) ?>" aria-label="تصویر <?= $gIdx + 1 ?>">
                            <img src="<?= e(product_image($gImg)) ?>" alt="<?= e($prodName) ?> - <?= $gIdx + 1 ?>" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="trust-row">
                <span class="trust-chip"><?= icon('shield', 14) ?> <?= __('guarantee_original', 'ضمانت اصالت') ?></span>
                <span class="trust-chip"><?= icon('refresh', 14) ?> <?= __('return_policy', '۷ روز بازگشت') ?></span>
                <span class="trust-chip"><?= icon('truck', 14) ?> <?= __('express_shipping', 'ارسال سریع') ?></span>
            </div>
        </div>

        <div class="product-summary">
            <div class="product-cat">
                <a href="/category/<?= e($product['category_slug']) ?>">
                    <?= icon($product['category_icon'] ?? 'box', 15) ?> <?= e($catName) ?>
                </a>
                <?php if ($product['brand']): ?><span class="brand-chip"><?= e($product['brand']) ?></span><?php endif; ?>
            </div>
            <h1><?= e($prodName) ?></h1>

            <div class="product-rating-row">
                <?= stars((float)$product['rating_avg']) ?>
                <span class="rating-num"><?= l_num(number_format((float)$product['rating_avg'], 1)) ?></span>
                <a href="#reviews" class="rating-link">(<?= l_num($product['rating_count']) ?> <?= __('reviews_count', ':count دیدگاه', ['count' => '']) ?>)</a>
                <span class="dot-sep">•</span>
                <span class="views-count"><?= icon('eye', 14) ?> <?= l_num($product['views']) ?> <?= __('views', 'بازدید') ?></span>
                <span class="dot-sep">•</span>
                <span class="views-count"><?= icon('check-circle', 14) ?> <?= l_num($product['sold']) ?> <?= __('sold_count', 'فروش') ?></span>
            </div>

            <?php if ($prodShort): ?>
                <p class="product-short"><?= e($prodShort) ?></p>
            <?php endif; ?>

            <div class="product-price-box">
                <?php if ($percent > 0): ?>
                    <div class="price-old-row">
                        <del><?= price($product['price']) ?></del>
                        <span class="save-badge"><?= __('your_savings', 'سود شما') ?>: <?= price($product['price'] - $final) ?></span>
                    </div>
                <?php endif; ?>
                <div class="price-final"><?= price($final) ?></div>
                <small class="muted"><?= __('approx_delivery', 'تحویل تقریبی: :date', ['date' => $deliveryDate]) ?></small>
            </div>

            <div class="stock-status">
                <?php if ($outOfStock): ?>
                    <span class="stock-out"><?= icon('x', 16) ?> <?= __('out_of_stock', 'ناموجود — به‌زودی شارژ می‌شود') ?></span>
                <?php elseif ($lowStock): ?>
                    <span class="stock-low"><?= icon('alert', 16) ?> <?= __('low_stock', 'تنها :count عدد باقی مانده', ['count' => l_num($product['stock'])]) ?></span>
                <?php else: ?>
                    <span class="stock-in"><?= icon('check-circle', 16) ?> <?= __('in_stock', 'موجود در انبار — آماده ارسال') ?></span>
                <?php endif; ?>
            </div>

            <?php if (!$outOfStock): ?>
            <form class="buy-box" id="buyForm">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <div class="qty-stepper" role="group" aria-label="<?= __('quantity', 'تعداد') ?>">
                    <button type="button" class="qty-btn" data-step="1" aria-label="+"><?= icon('plus', 16) ?></button>
                    <input type="text" name="qty" id="qtyInput" value="<?= l_num(max(1, $inCartQty)) ?>" inputmode="numeric" max="<?= (int)$product['stock'] ?>" aria-label="<?= __('quantity', 'تعداد') ?>">
                    <button type="button" class="qty-btn" data-step="-1" aria-label="-"><?= icon('minus', 16) ?></button>
                </div>
                <button type="submit" class="btn btn-primary btn-lg add-to-cart-detail">
                    <?= icon('cart', 20) ?> <?= __('add_to_cart', 'افزودن به سبد خرید') ?>
                </button>
                <button type="button" class="btn btn-outline btn-icon-lg wishlist-btn <?= $inWishlist ? 'active' : '' ?>" data-product="<?= (int)$product['id'] ?>" title="<?= __('wishlist', 'علاقه‌مندی') ?>" aria-label="<?= __('wishlist', 'علاقه‌مندی') ?>">
                    <?= icon('heart', 20) ?>
                </button>
            </form>
            <?php else: ?>
                <button class="btn btn-outline btn-lg" disabled><?= __('out_of_stock', 'ناموجود — به‌زودی شارژ می‌شود') ?></button>
            <?php endif; ?>

            <div class="product-perks">
                <span><?= icon('shield', 16) ?> <?= __('guarantee_original', 'ضمانت اصالت کالا') ?></span>
                <span><?= icon('truck', 16) ?> <?= __('express_shipping', 'ارسال سریع') ?></span>
                <span><?= icon('refresh', 16) ?> <?= __('return_policy', '۷ روز بازگشت') ?></span>
                <span><?= icon('credit-card', 16) ?> <?= __('secure_payment', 'پرداخت امن') ?></span>
            </div>
        </div>
    </div>

    <!-- تب‌ها (توضیحات محصول، مشخصات فنی، نظرات کاربران) -->
    <div class="tabs card in" id="productTabs" style="opacity: 1 !important; transform: none !important; margin-top: 24px;">
        <div class="tab-btns" role="tablist">
            <button class="tab-btn active" data-tab="desc" role="tab" type="button"><?= __('tab_description', 'توضیحات') ?></button>
            <button class="tab-btn" data-tab="specs" role="tab" type="button"><?= __('tab_specs', 'مشخصات فنی') ?></button>
            <button class="tab-btn" data-tab="reviews" role="tab" type="button"><?= __('tab_reviews', 'دیدگاه‌ها') ?> (<?= l_num(count($reviews)) ?>)</button>
        </div>

        <div class="tab-panel active" id="tab-desc">
            <div class="prose" style="line-height: 1.9; font-size: 15px;">
                <?php if ($prodDesc): ?>
                    <?php foreach (explode("\n", (string)$prodDesc) as $para): ?>
                        <?php if (trim($para) !== ''): ?><p style="margin-bottom: 14px;"><?= e(trim($para)) ?></p><?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted"><?= __('no_desc', 'توضیحاتی برای این محصول ثبت نشده است.') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-panel" id="tab-specs">
            <?php if (!empty($prodSpecs)): ?>
                <table class="specs-table">
                    <tbody>
                    <?php foreach ($prodSpecs as $key => $val): ?>
                        <tr>
                            <th style="width: 28%;"><?= e($key) ?></th>
                            <td><?= e(is_array($val) ? implode('، ', $val) : $val) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="muted"><?= __('no_specs', 'مشخصات فنی برای این محصول ثبت نشده است.') ?></p>
            <?php endif; ?>
        </div>

        <div class="tab-panel" id="tab-reviews">
            <div class="reviews-layout" id="reviews">
                <div class="reviews-summary">
                    <div class="rating-big"><?= l_num(number_format((float)$product['rating_avg'], 1)) ?></div>
                    <?= stars((float)$product['rating_avg']) ?>
                    <span class="muted"><?= __('rating_overview', 'امتیاز') ?> (<?= l_num($product['rating_count']) ?> <?= __('reviews_count', 'دیدگاه', ['count' => '']) ?>)</span>
                    <div class="rating-bars">
                        <?php for ($i = 5; $i >= 1; $i--):
                            $cnt = $ratingDist[$i] ?? 0;
                            $pct = count($reviews) ? round($cnt / count($reviews) * 100) : 0;
                        ?>
                            <div class="rating-bar-row">
                                <span><?= l_num($i) ?> ★</span>
                                <div class="rating-bar"><i style="width:<?= $pct ?>%"></i></div>
                                <span><?= l_num($cnt) ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <?php if (auth() && $canReview): ?>
                        <form action="/review" method="post" class="review-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <h4><?= __('write_review', 'دیدگاه خود را بنویسید') ?></h4>
                            <div class="star-input" id="starInput">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                    <label for="star<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </div>
                            <input type="text" name="title" placeholder="<?= __('review_title', 'عنوان دیدگاه (اختیاری)') ?>" maxlength="150">
                            <textarea name="comment" rows="4" required minlength="10" maxlength="2000" placeholder="<?= __('review_comment', 'تجربه خود از این محصول را بنویسید...') ?>"></textarea>
                            <button type="submit" class="btn btn-primary"><?= __('submit_review', 'ثبت دیدگاه') ?></button>
                        </form>
                    <?php elseif (auth() && $hasReviewed): ?>
                        <p class="muted small"><?= __('already_reviewed', 'شما قبلاً برای این محصول دیدگاه ثبت کرده‌اید.') ?></p>
                    <?php else: ?>
                        <a href="/login" class="btn btn-outline btn-block"><?= __('login_to_review', 'برای ثبت دیدگاه وارد شوید') ?></a>
                    <?php endif; ?>
                </div>

                <div class="reviews-list">
                    <?php if ($reviews): ?>
                        <?php foreach ($reviews as $r): ?>
                            <div class="review-item">
                                <div class="review-head">
                                    <span class="avatar sm"><?= e(mb_substr($r['user_name'], 0, 1)) ?></span>
                                    <div>
                                        <strong><?= e($r['user_name']) ?></strong>
                                        <span class="muted small"><?= localized_date(is_numeric($r['created_at']) ? (int)$r['created_at'] : strtotime($r['created_at'])) ?></span>
                                    </div>
                                    <?= stars((float)$r['rating']) ?>
                                </div>
                                <?php if ($r['title']): ?><strong class="review-title"><?= e($r['title']) ?></strong><?php endif; ?>
                                <p><?= e(!I18n::isRtl() && !empty($r['comment_en']) ? $r['comment_en'] : $r['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <span class="empty-icon empty-svg"><?= icon('message', 48) ?></span>
                            <p><?= __('no_reviews_yet', 'هنوز دیدگاهی ثبت نشده است. اولین نفری باشید که نظر می‌دهد.') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- محصولات مرتبط -->
    <?php if ($related): ?>
    <section class="section" style="margin-top: 40px;">
        <div class="section-head reveal in">
            <div>
                <span class="eyebrow"><?= __('you_might_also_like', 'شاید بپسندید') ?></span>
                <h2><?= __('related_products', 'محصولات مرتبط') ?></h2>
            </div>
        </div>
        <div class="product-grid cols-4">
            <?php foreach ($related as $rp): ?>
                <?php partial('partials/product-card', ['product' => $rp]); ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>
