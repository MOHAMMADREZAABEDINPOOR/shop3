<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'درباره ما']]]); ?>
    <div class="page-hero">
        <span class="eyebrow">داستان ما</span>
        <h1>درباره <?= e(config('app_name')) ?></h1>
        <p>از یک ایده ساده شروع کردیم: خرید آنلاین باید آسان، مطمئن و لذت‌بخش باشد — نه پر از تردید.</p>
    </div>

    <div class="stats-row" style="margin-bottom:20px">
        <div class="stat-card card reveal"><span class="stat-icon" style="--c:#6d28d9"><?= icon('package', 22) ?></span><div><strong><?= fa_num('+۴۸') ?></strong><span>کالای اورجینال</span></div></div>
        <div class="stat-card card reveal"><span class="stat-icon" style="--c:#059669"><?= icon('users', 22) ?></span><div><strong><?= fa_num('۹۸٪') ?></strong><span>رضایت مشتریان</span></div></div>
        <div class="stat-card card reveal"><span class="stat-icon" style="--c:#d97706"><?= icon('truck', 22) ?></span><div><strong><?= fa_num('۷۲') ?> ساعته</strong><span>حداکثر زمان ارسال</span></div></div>
        <div class="stat-card card reveal"><span class="stat-icon" style="--c:#e11d48"><?= icon('shield', 22) ?></span><div><strong><?= fa_num('۱۰۰٪') ?></strong><span>ضمانت اصالت</span></div></div>
    </div>

    <div class="prose card reveal">
        <h2>ما که هستیم</h2>
        <p><?= e(config('app_name')) ?> فروشگاه آنلاینی است که با وسواس روی سه چیز ساخته شده: کالای اورجینال، قیمت منصفانه و احترام به مشتری. هر کالا پیش از ارسال بررسی می‌شود، با فاکتور رسمی و بسته‌بندی ایمن به دست شما می‌رسد.</p>
        <h2>چرا به ما اعتماد کنید</h2>
        <p>ضمانت ۷ روزه بازگشت بدون قید و شرط، پشتیبانی واقعی ۷ روز هفته، پرداخت امن بانکی و شفافیت کامل در قیمت و موجودی — این‌ها شعار نیستند؛ سازوکار روزانه ما هستند.</p>
        <h2 id="faq">سوالات متداول</h2>
        <details><summary>چطور سفارشم را پیگیری کنم؟</summary><p>از صفحه «پیگیری سفارش» با کد سفارش و شماره موبایل، یا از بخش «سفارش‌های من» در حساب کاربری.</p></details>
        <details><summary>شرایط بازگشت کالا چیست؟</summary><p>تا ۷ روز پس از تحویل، کالای استفاده‌نشده با بسته‌بندی سالم بدون قید و شرط پس گرفته می‌شود. جزئیات کامل در <a href="/terms">قوانین و مقررات</a>.</p></details>
        <details><summary>ارسال به شهرستان چقدر طول می‌کشد؟</summary><p>معمولاً بین ۲۴ تا ۷۲ ساعت کاری. سفارش‌های بالای <?= price(config('free_shipping_threshold')) ?> ارسال رایگان دارند.</p></details>
        <details><summary>اطلاعات من امن است؟</summary><p>بله. رمزها هش‌شده، داده‌های حساس رمزنگاری‌شده و پرداخت مستقیماً از درگاه بانکی انجام می‌شود. جزئیات در <a href="/privacy">حریم خصوصی</a>.</p></details>
        <h2 id="rules">قوانین و مقررات</h2>
        <p>متن کامل قوانین خرید، بازگشت و گارانتی را در صفحه <a href="/terms">قوانین و مقررات</a> بخوانید.</p>
    </div>
</div>
