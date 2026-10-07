<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;

class OrderController
{
    public function index(): void
    {
        $status = (string)input('status', '');
        $q      = trim((string)input('q', ''));
        $where  = ['1=1'];
        $params = [];
        if ($status !== '' && in_array($status, order_statuses(), true)) {
            $where[]  = 'o.status = ?';
            $params[] = $status;
        }
        if ($q !== '') {
            $where[] = '(o.order_code LIKE ? OR o.receiver_name LIKE ? OR o.receiver_phone LIKE ? OR u.email LIKE ?)';
            array_push($params, "%{$q}%", "%{$q}%", "%{$q}%", "%{$q}%");
        }
        $orders = DB::fetchAll(
            "SELECT o.*, u.name AS user_name, u.email AS user_email, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items_count
             FROM orders o JOIN users u ON u.id = o.user_id WHERE " . implode(' AND ', $where) . " ORDER BY o.created_at DESC",
            $params
        );
        $counts = [];
        foreach (DB::fetchAll("SELECT status, COUNT(*) AS c FROM orders GROUP BY status") as $r) {
            $counts[$r['status']] = (int)$r['c'];
        }
        view('admin/orders/index', ['title' => 'مدیریت سفارش‌ها', 'orders' => $orders, 'status' => $status, 'q' => $q, 'counts' => $counts], 'admin');
    }

    public function show(string $id): void
    {
        $order = DB::fetch("SELECT o.*, u.name AS user_name, u.email AS user_email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?", [(int)$id]);
        if (!$order) {
            redirect('/admin/orders');
        }
        $items = DB::fetchAll("SELECT oi.*, p.slug, p.stock FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?", [$order['id']]);
        view('admin/orders/show', ['title' => 'سفارش ' . $order['order_code'], 'order' => $order, 'items' => $items], 'admin');
    }

    public function updateStatus(string $id): void
    {
        abort_csrf();
        $status = (string)input('status', '');
        $order  = DB::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
        if (!$order || !in_array($status, order_statuses(), true)) {
            flash('error', 'وضعیت نامعتبر است.', 'error');
            redirect('/admin/orders/' . $id);
        }

        // لغو سفارش → بازگرداندن موجودی
        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            foreach (DB::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]) as $it) {
                if ($it['product_id']) {
                    DB::query("UPDATE products SET stock = stock + ?, sold = CASE WHEN sold - ? < 0 THEN 0 ELSE sold - ? END WHERE id = ?", [$it['qty'], $it['qty'], $it['qty'], $it['product_id']]);
                }
            }
        }
        // بازگشت از لغو → کسر مجدد موجودی
        if ($order['status'] === 'cancelled' && $status !== 'cancelled') {
            foreach (DB::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]) as $it) {
                if ($it['product_id']) {
                    DB::query("UPDATE products SET stock = CASE WHEN stock - ? < 0 THEN 0 ELSE stock - ? END, sold = sold + ? WHERE id = ?", [$it['qty'], $it['qty'], $it['qty'], $it['product_id']]);
                }
            }
        }

        $update = ['status' => $status];
        if ($status === 'paid' && !$order['paid_at']) {
            $update['paid_at'] = date('Y-m-d H:i:s');
        }
        DB::update('orders', $update, 'id = :id', ['id' => $order['id']]);
        flash('success', 'وضعیت سفارش به «' . order_status($status)['label'] . '» تغییر کرد.');
        redirect('/admin/orders/' . $id);
    }
}
