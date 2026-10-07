<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;
use App\Controllers\ReviewController;

class DashboardController
{
    public function index(): void
    {
        $paidStatuses = "('paid','processing','shipped','delivered')";

        $stats = [
            'revenue'        => (int)DB::value("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN {$paidStatuses}"),
            'revenue_month'  => (int)DB::value("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN {$paidStatuses} AND created_at >= ?", [date('Y-m-d 00:00:00', strtotime('-30 days'))]),
            'orders'         => (int)DB::value("SELECT COUNT(*) FROM orders"),
            'orders_pending' => (int)DB::value("SELECT COUNT(*) FROM orders WHERE status IN ('paid','processing')"),
            'products'       => (int)DB::value("SELECT COUNT(*) FROM products"),
            'low_stock'      => (int)DB::value("SELECT COUNT(*) FROM products WHERE stock <= 5"),
            'users'          => (int)DB::value("SELECT COUNT(*) FROM users WHERE role = 'customer'"),
            'users_week'     => (int)DB::value("SELECT COUNT(*) FROM users WHERE created_at >= ?", [date('Y-m-d 00:00:00', strtotime('-7 days'))]),
            'reviews'        => (int)DB::value("SELECT COUNT(*) FROM reviews"),
        ];

        // فروش ۳۰ روز اخیر (برای نمودار)
        $rows = DB::fetchAll(
            "SELECT DATE(created_at) AS d, COALESCE(SUM(total),0) AS s, COUNT(*) AS c FROM orders WHERE status IN {$paidStatuses} AND created_at >= ? GROUP BY DATE(created_at)",
            [date('Y-m-d 00:00:00', strtotime('-29 days'))]
        );
        $byDay = [];
        foreach ($rows as $r) {
            $byDay[$r['d']] = $r;
        }
        $chart = ['labels' => [], 'sales' => [], 'orders' => []];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $chart['labels'][] = jdate($d, 'short');
            $chart['sales'][]  = (int)($byDay[$d]['s'] ?? 0);
            $chart['orders'][] = (int)($byDay[$d]['c'] ?? 0);
        }

        // وضعیت سفارش‌ها
        $statusRows = DB::fetchAll("SELECT status, COUNT(*) AS c FROM orders GROUP BY status");
        $statusChart = [];
        foreach ($statusRows as $r) {
            $statusChart[] = ['label' => order_status($r['status'])['label'], 'value' => (int)$r['c'], 'status' => $r['status']];
        }

        // فروش بر اساس دسته‌بندی
        $catSales = DB::fetchAll(
            "SELECT c.name, c.color, COALESCE(SUM(oi.price * oi.qty),0) AS s FROM order_items oi
             JOIN orders o ON o.id = oi.order_id JOIN products p ON p.id = oi.product_id JOIN categories c ON c.id = p.category_id
             WHERE o.status IN {$paidStatuses} GROUP BY c.id, c.name, c.color ORDER BY s DESC"
        );

        $recentOrders = DB::fetchAll("SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 8");
        $topProducts  = DB::fetchAll("SELECT * FROM products ORDER BY sold DESC LIMIT 5");
        $lowStock     = DB::fetchAll("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5");

        view('admin/dashboard', [
            'title'        => 'داشبورد مدیریت',
            'stats'        => $stats,
            'chart'        => $chart,
            'statusChart'  => $statusChart,
            'catSales'     => $catSales,
            'recentOrders' => $recentOrders,
            'topProducts'  => $topProducts,
            'lowStock'     => $lowStock,
        ], 'admin');
    }

    public function reviews(): void
    {
        $reviews = DB::fetchAll(
            "SELECT r.*, u.name AS user_name, p.name AS product_name, p.slug FROM reviews r JOIN users u ON u.id = r.user_id JOIN products p ON p.id = r.product_id ORDER BY r.created_at DESC"
        );
        view('admin/reviews', ['title' => 'مدیریت نظرات', 'reviews' => $reviews], 'admin');
    }

    public function toggleReview(string $id): void
    {
        abort_csrf();
        $r = DB::fetch("SELECT * FROM reviews WHERE id = ?", [(int)$id]);
        if ($r) {
            DB::update('reviews', ['is_approved' => $r['is_approved'] ? 0 : 1], 'id = :id', ['id' => $r['id']]);
            ReviewController::recalculate((int)$r['product_id']);
            flash('success', 'وضعیت نظر تغییر کرد.');
        }
        redirect('/admin/reviews');
    }

    public function deleteReview(string $id): void
    {
        abort_csrf();
        $r = DB::fetch("SELECT * FROM reviews WHERE id = ?", [(int)$id]);
        if ($r) {
            DB::delete('reviews', 'id = ?', [$r['id']]);
            ReviewController::recalculate((int)$r['product_id']);
            flash('success', 'نظر حذف شد.');
        }
        redirect('/admin/reviews');
    }
}
