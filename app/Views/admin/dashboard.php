<div class="stats-grid">
    <div class="stat-card card">
        <span class="stat-icon" style="--c:#6366f1"><?= icon('wallet', 22) ?></span>
        <div><strong><?= price($stats['revenue']) ?></strong><span>درآمد کل</span></div>
        <small class="stat-sub">۳۰ روز اخیر: <?= price($stats['revenue_month']) ?></small>
    </div>
    <div class="stat-card card">
        <span class="stat-icon" style="--c:#f59e0b"><?= icon('package', 22) ?></span>
        <div><strong><?= fa_num($stats['orders']) ?></strong><span>سفارش</span></div>
        <small class="stat-sub"><?= fa_num($stats['orders_pending']) ?> در انتظار اقدام</small>
    </div>
    <div class="stat-card card">
        <span class="stat-icon" style="--c:#10b981"><?= icon('grid', 22) ?></span>
        <div><strong><?= fa_num($stats['products']) ?></strong><span>محصول</span></div>
        <small class="stat-sub text-danger"><?= fa_num($stats['low_stock']) ?> رو به اتمام</small>
    </div>
    <div class="stat-card card">
        <span class="stat-icon" style="--c:#0ea5e9"><?= icon('users', 22) ?></span>
        <div><strong><?= fa_num($stats['users']) ?></strong><span>مشتری</span></div>
        <small class="stat-sub"><?= fa_num($stats['users_week']) ?> عضو جدید این هفته</small>
    </div>
</div>

<div class="admin-grid-2">
    <div class="card">
        <div class="card-head"><h3><?= icon('trending-up', 20) ?> فروش ۳۰ روز اخیر</h3></div>
        <div class="chart-box"><canvas id="salesChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-head"><h3><?= icon('bar-chart', 20) ?> وضعیت سفارش‌ها</h3></div>
        <div class="chart-box"><canvas id="statusChart"></canvas></div>
    </div>
</div>

<div class="admin-grid-2">
    <div class="card">
        <div class="card-head">
            <h3><?= icon('package', 20) ?> آخرین سفارش‌ها</h3>
            <a href="/admin/orders" class="btn btn-ghost btn-sm">همه <?= icon('arrow-left', 14) ?></a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>کد</th><th>مشتری</th><th>مبلغ</th><th>وضعیت</th></tr></thead>
                <tbody>
                <?php foreach ($recentOrders as $o): $st = order_status($o['status']); ?>
                    <tr onclick="location.href='/admin/orders/<?= (int)$o['id'] ?>'" style="cursor:pointer">
                        <td><b dir="ltr"><?= e($o['order_code']) ?></b></td>
                        <td><?= e($o['user_name']) ?></td>
                        <td><?= price($o['total']) ?></td>
                        <td><span class="status-badge st-<?= $st['color'] ?>"><?= $st['label'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h3><?= icon('award', 20) ?> پرفروش‌ترین محصولات</h3></div>
        <div class="top-products">
            <?php foreach ($topProducts as $p): ?>
                <a href="/product/<?= e($p['slug']) ?>" class="top-product">
                    <img src="<?= e(product_image($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                    <div><strong><?= e($p['name']) ?></strong><span class="muted small"><?= fa_num($p['sold']) ?> فروش</span></div>
                    <b><?= price(final_price($p)) ?></b>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ($lowStock): ?>
            <div class="low-stock-alert">
                <?= icon('alert', 18) ?> <b>هشدار موجودی:</b>
                <?php foreach ($lowStock as $p): ?>
                    <span class="low-stock-chip"><?= e($p['name']) ?> (<?= fa_num($p['stock']) ?>)</span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($catSales): ?>
<div class="card">
    <div class="card-head"><h3><?= icon('layers', 20) ?> فروش بر اساس دسته‌بندی</h3></div>
    <div class="chart-box sm"><canvas id="catChart"></canvas></div>
</div>
<?php endif; ?>

<?php ob_start(); ?>
<script>
(function(){
    var chartData = <?= json_encode($chart, JSON_UNESCAPED_UNICODE) ?>;
    var statusData = <?= json_encode($statusChart, JSON_UNESCAPED_UNICODE) ?>;
    var catData = <?= json_encode($catSales, JSON_UNESCAPED_UNICODE) ?>;
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.06)';
    var textColor = isDark ? '#94a3b8' : '#64748b';
    Chart.defaults.font.family = 'Vazirmatn, Tahoma, sans-serif';
    Chart.defaults.color = textColor;

    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'فروش (تومان)',
                data: chartData.sales,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,.12)',
                fill: true, tension: .4, pointRadius: 0, borderWidth: 2.5
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: {
                rtl: true, textDirection: 'rtl',
                callbacks: { label: function(c){ return ' ' + c.parsed.y.toLocaleString('fa-IR') + ' تومان'; } }
            }},
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                y: { grid: { color: gridColor }, ticks: { callback: function(v){ return (v/1000000).toLocaleString('fa-IR') + 'M'; } } }
            }
        }
    });

    var statusColors = { pending:'#f59e0b', paid:'#0ea5e9', processing:'#6366f1', shipped:'#8b5cf6', delivered:'#10b981', cancelled:'#ef4444' };
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(function(s){ return s.label; }),
            datasets: [{ data: statusData.map(function(s){ return s.value; }), backgroundColor: statusData.map(function(s){ return statusColors[s.status] || '#94a3b8'; }), borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'left', rtl: true } } }
    });

    var catEl = document.getElementById('catChart');
    if (catEl) {
        new Chart(catEl, {
            type: 'bar',
            data: {
                labels: catData.map(function(c){ return c.name; }),
                datasets: [{ data: catData.map(function(c){ return c.s; }), backgroundColor: catData.map(function(c){ return c.color; }), borderRadius: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { rtl: true, callbacks: { label: function(c){ return ' ' + c.parsed.x.toLocaleString('fa-IR') + ' تومان'; } } } },
                scales: { x: { grid: { color: gridColor }, ticks: { callback: function(v){ return (v/1000000).toLocaleString('fa-IR') + 'M'; } } }, y: { grid: { display: false } } }
            }
        });
    }
})();
</script>
<?php $scripts = ob_get_clean(); ?>
