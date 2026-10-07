<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'حساب کاربری', 'url' => '/account'], ['label' => 'علاقه‌مندی‌ها']]]); ?>
    <h1 class="page-title"><?= icon('heart', 28) ?> علاقه‌مندی‌ها</h1>

    <?php if (!$products): ?>
        <div class="empty-state card">
            <span class="empty-icon empty-svg"><?= icon('heart', 48) ?></span>
            <h3>لیست علاقه‌مندی‌ها خالی است</h3>
            <p>با زدن دکمه قلب روی هر کالا، آن را اینجا ذخیره کنید.</p>
            <a href="/shop" class="btn btn-primary">مشاهده محصولات</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <?php partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
