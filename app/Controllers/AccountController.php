<?php
namespace App\Controllers;

use App\Core\Database as DB;
use App\Core\Auth;

class AccountController
{
    public function index(): void
    {
        $uid = Auth::id();
        $stats = [
            'orders'    => (int)DB::value("SELECT COUNT(*) FROM orders WHERE user_id = ?", [$uid]),
            'spent'     => (int)DB::value("SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id = ? AND status NOT IN ('pending','cancelled')", [$uid]),
            'wishlist'  => (int)DB::value("SELECT COUNT(*) FROM wishlists WHERE user_id = ?", [$uid]),
            'reviews'   => (int)DB::value("SELECT COUNT(*) FROM reviews WHERE user_id = ?", [$uid]),
        ];
        $recentOrders = DB::fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$uid]);

        view('account/index', [
            'title'        => 'حساب کاربری | ' . config('app_name'),
            'meta_description' => 'مدیریت حساب کاربری: پروفایل، سفارش‌ها و علاقه‌مندی‌ها.',
            'user'         => auth(),
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function updateProfile(): void
    {
        abort_csrf();
        $name  = trim((string)input('name', ''));
        $phone = en_num(trim((string)input('phone', '')));
        $city  = trim((string)input('city', ''));
        $address = trim((string)input('address', ''));
        $postal  = en_num(trim((string)input('postal_code', '')));

        $errors = [];
        if (mb_strlen($name) < 3) $errors[] = 'نام باید حداقل ۳ حرف باشد.';
        if ($phone !== '' && !preg_match('/^09\d{9}$/', $phone)) $errors[] = 'شماره موبایل معتبر نیست.';
        if ($postal !== '' && !preg_match('/^\d{10}$/', $postal)) $errors[] = 'کد پستی باید ۱۰ رقم باشد.';

        if ($errors) {
            flash('error', implode('<br>', $errors), 'error');
            redirect('/account');
        }

        DB::update('users', [
            'name'        => $name,
            'phone'       => $phone ?: null,
            'city'        => $city ?: null,
            'address'     => $address ?: null,
            'postal_code' => $postal ?: null,
        ], 'id = :id', ['id' => Auth::id()]);

        flash('success', 'اطلاعات پروفایل با موفقیت ذخیره شد.');
        redirect('/account');
    }

    public function updatePassword(): void
    {
        abort_csrf();
        $current = (string)input('current_password', '');
        $new     = (string)input('password', '');
        $confirm = (string)input('password_confirm', '');

        if (!password_verify($current, auth()['password'])) {
            flash('error', 'رمز عبور فعلی اشتباه است.', 'error');
            redirect('/account');
        }
        if (strlen($new) < 6) {
            flash('error', 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد.', 'error');
            redirect('/account');
        }
        if ($new !== $confirm) {
            flash('error', 'تکرار رمز عبور مطابقت ندارد.', 'error');
            redirect('/account');
        }
        DB::update('users', ['password' => password_hash($new, PASSWORD_DEFAULT)], 'id = :id', ['id' => Auth::id()]);
        flash('success', 'رمز عبور با موفقیت تغییر کرد.');
        redirect('/account');
    }

    public function orders(): void
    {
        $orders = DB::fetchAll(
            "SELECT o.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items_count FROM orders o WHERE user_id = ? ORDER BY created_at DESC",
            [Auth::id()]
        );
        view('account/orders', ['title' => 'سفارش‌های من | ' . config('app_name'), 'meta_description' => 'تاریخچه و وضعیت سفارش‌های شما.', 'orders' => $orders]);
    }

    public function order(string $code): void
    {
        $order = DB::fetch("SELECT * FROM orders WHERE order_code = ? AND user_id = ?", [$code, Auth::id()]);
        if (!$order) {
            http_response_code(404);
            view('errors/404', ['title' => 'سفارش پیدا نشد']);
            return;
        }
        $items = DB::fetchAll("SELECT oi.*, p.slug FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?", [$order['id']]);
        view('account/order', ['title' => 'سفارش ' . $order['order_code'], 'order' => $order, 'items' => $items]);
    }

    public function wishlist(): void
    {
        $products = DB::fetchAll(
            "SELECT p.* FROM wishlists w JOIN products p ON p.id = w.product_id WHERE w.user_id = ? ORDER BY w.created_at DESC",
            [Auth::id()]
        );
        view('account/wishlist', ['title' => 'علاقه‌مندی‌ها | ' . config('app_name'), 'meta_description' => 'کالاهای ذخیره‌شده در لیست علاقه‌مندی‌های شما.', 'products' => $products]);
    }

    public function toggleWishlist(): void
    {
        abort_csrf();
        $pid = (int)en_num(input('product_id', 0));
        $exists = DB::fetch("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?", [Auth::id(), $pid]);
        if ($exists) {
            DB::delete('wishlists', 'id = ?', [$exists['id']]);
            $res = ['ok' => true, 'active' => false, 'message' => 'از علاقه‌مندی‌ها حذف شد.'];
        } else {
            if (!DB::fetch("SELECT id FROM products WHERE id = ?", [$pid])) {
                json_response(['ok' => false, 'message' => 'محصول پیدا نشد.'], 404);
            }
            DB::insert('wishlists', ['user_id' => Auth::id(), 'product_id' => $pid]);
            $res = ['ok' => true, 'active' => true, 'message' => 'به علاقه‌مندی‌ها اضافه شد.'];
        }
        if (is_ajax()) {
            json_response($res);
        }
        flash('success', $res['message']);
        back();
    }
}
