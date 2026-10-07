<div class="container page">
    <div class="error-page card reveal in">
        <div class="error-art"><?= icon('search', 56) ?></div>
        <div class="error-code">۴۰۴</div>
        <h1>این صفحه پیدا نشد</h1>
        <p class="muted">آدرس اشتباه وارد شده یا صفحه منتقل شده است. نگران نباشید — هزاران کالای دیگر در انتظار شماست.</p>
        <form action="/search" method="get" class="search-form error-search" role="search">
            <span class="search-icon"><?= icon('search', 20) ?></span>
            <input type="search" name="q" placeholder="دنبال چه کالایی هستید؟" aria-label="جستجو" maxlength="100">
            <button type="submit" class="search-btn">جستجو</button>
        </form>
        <div class="error-actions">
            <a href="/" class="btn btn-primary"><?= icon('home', 18) ?> بازگشت به خانه</a>
            <a href="/shop" class="btn btn-outline"><?= icon('grid', 18) ?> مشاهده محصولات</a>
        </div>
    </div>
</div>
