<div class="payment-page">
    <div class="payment-gateway card">
        <div class="gateway-header">
            <span class="gateway-logo"><?= icon('lock', 22) ?></span>
            <div>
                <strong>درگاه پرداخت امن نکست‌پی</strong>
                <small>پرداخت شبیه‌سازی‌شده (دمو)</small>
            </div>
        </div>
        <div class="gateway-order">
            <div class="summary-row"><span>شماره سفارش</span><b dir="ltr"><?= e($order['order_code']) ?></b></div>
            <div class="summary-row"><span>گیرنده</span><b><?= e($order['receiver_name']) ?></b></div>
            <div class="summary-row total"><span>مبلغ قابل پرداخت</span><strong><?= price($order['total']) ?></strong></div>
        </div>
        <form method="post" action="/payment/<?= e($order['order_code']) ?>/verify" id="paymentForm">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>شماره کارت</label>
                <input type="text" dir="ltr" placeholder="۶۰۳۷ - ۹۹۷۷ - ۱۲۳۴ - ۵۶۷۸" maxlength="19" class="card-input">
            </div>
            <div class="form-row">
                <div class="form-group"><label>CVV2</label><input type="text" dir="ltr" placeholder="۱۲۳" maxlength="4" class="card-input"></div>
                <div class="form-group"><label>تاریخ انقضا</label><input type="text" dir="ltr" placeholder="۰۴/۰۸" maxlength="5" class="card-input"></div>
            </div>
            <div class="gateway-actions">
                <button type="submit" name="result" value="success" class="btn btn-primary btn-lg btn-block" id="payBtn"><?= icon('check-circle', 20) ?> پرداخت <?= price($order['total']) ?></button>
                <button type="submit" name="result" value="fail" class="btn btn-ghost btn-block">انصراف از پرداخت</button>
            </div>
        </form>
        <p class="secure-note center"><?= icon('shield', 14) ?> این یک درگاه نمایشی است؛ هیچ پرداخت واقعی انجام نمی‌شود.</p>
    </div>
</div>
<script>
document.getElementById('paymentForm').addEventListener('submit', function(e){
    var btn = document.getElementById('payBtn');
    if (e.submitter && e.submitter.value === 'success') {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> در حال اتصال به بانک...';
    }
});
</script>
