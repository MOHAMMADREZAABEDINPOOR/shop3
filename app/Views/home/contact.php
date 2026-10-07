<?php $contact = config('contact', []); ?>
<div class="container page">
    <?php partial('partials/breadcrumb', ['items' => [['label' => 'تماس با ما']]]); ?>
    <div class="page-hero">
        <span class="eyebrow">پاسخگوی شما هستیم</span>
        <h1>تماس با ما</h1>
        <p>سوال، پیشنهاد یا مشکلی دارید؟ در ساعات کاری زیر یک دقیقه پاسخ می‌گیرید.</p>
    </div>
    <div class="contact-grid">
        <div class="contact-cards">
            <div class="card contact-card reveal"><?= icon('phone', 26) ?><h3>تلفن پشتیبانی</h3><p dir="ltr"><?= fa_num($contact['phone'] ?? '') ?></p><span><?= e($contact['work_hours'] ?? '') ?></span></div>
            <div class="card contact-card reveal"><?= icon('mail', 26) ?><h3>ایمیل</h3><p dir="ltr"><?= e($contact['email'] ?? '') ?></p><span>پاسخ در کمتر از ۲۴ ساعت</span></div>
            <div class="card contact-card reveal"><?= icon('map-pin', 26) ?><h3>دفتر مرکزی</h3><p><?= e($contact['address'] ?? '') ?></p><span>مراجعه حضوری با هماهنگی قبلی</span></div>
        </div>
        <div class="card contact-form-card reveal">
            <h2>پیام بفرستید</h2>
            <form id="contactForm" novalidate>
                <?= honeypot_field() ?>
                <div class="form-row">
                    <div class="form-group"><label for="cName">نام و نام خانوادگی</label><input type="text" id="cName" required placeholder="مثلاً: رضا احمدی" maxlength="120"></div>
                    <div class="form-group"><label for="cEmail">ایمیل</label><input type="email" id="cEmail" required placeholder="you@example.com" dir="ltr" maxlength="190"></div>
                </div>
                <div class="form-group"><label for="cSubject">موضوع</label><input type="text" id="cSubject" required placeholder="موضوع پیام" maxlength="150"></div>
                <div class="form-group"><label for="cMsg">متن پیام</label><textarea id="cMsg" rows="5" required placeholder="پیام خود را بنویسید..." maxlength="2000"></textarea></div>
                <button class="btn btn-primary btn-lg"><?= icon('mail', 18) ?> ارسال پیام</button>
            </form>
        </div>
    </div>
</div>
