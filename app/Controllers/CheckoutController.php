<?php
namespace App\Controllers;

use App\Core\Database as DB;
use App\Core\Auth;

class CheckoutController
{
    public function index(): void
    {
        $cart = cart();
        if ($cart->isEmpty()) {
            flash('warning', 'سبد خرید شما خالی است.', 'warning');
            redirect('/shop');
        }
        $isEn = !\App\Core\I18n::isRtl();
        view('checkout/index', [
            'title' => __('checkout', 'تسویه حساب') . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => $isEn
                ? 'Complete your order at ' . __('site_title', 'NextShop') . ' with secure payment options.'
                : 'تکمیل سفارش در ' . config('app_name') . ' با پرداخت امن آنلاین یا پرداخت در محل.',
            'items' => $cart->items(),
            'cart'  => $cart,
            'user'  => auth(),
        ]);
    }

    /** ثبت سفارش */
    public function place(): void
    {
        abort_csrf();
        $cart = cart();
        if ($cart->isEmpty()) {
            redirect('/cart');
        }

        $data = [
            'receiver_name'  => mb_substr(trim((string)input('receiver_name')), 0, 120),
            'receiver_phone' => en_num(trim((string)input('receiver_phone'))),
            'city'           => mb_substr(trim((string)input('city')), 0, 100),
            'address'        => mb_substr(trim((string)input('address')), 0, 500),
            'postal_code'    => en_num(trim((string)input('postal_code', ''))),
            'note'           => mb_substr(trim((string)input('note', '')), 0, 500),
            'payment_method' => input('payment_method', 'online') === 'cod' ? 'cod' : 'online',
        ];

        $errors = [];
        if (mb_strlen($data['receiver_name']) < 3) $errors['receiver_name'] = 'نام گیرنده را کامل وارد کنید.';
        if (!preg_match('/^09\d{9}$/', $data['receiver_phone'])) $errors['receiver_phone'] = 'شماره موبایل معتبر نیست (مثال: ۰۹۱۲۱۲۳۴۵۶۷).';
        if (mb_strlen($data['city']) < 2) $errors['city'] = 'شهر را وارد کنید.';
        if (mb_strlen($data['address']) < 10) $errors['address'] = 'آدرس را کامل‌تر وارد کنید (حداقل ۱۰ حرف).';
        if ($data['postal_code'] !== '' && !preg_match('/^\d{10}$/', $data['postal_code'])) $errors['postal_code'] = 'کد پستی باید ۱۰ رقم باشد.';
        if (input('terms') !== '1') $errors['terms'] = 'برای ثبت سفارش باید قوانین فروشگاه را بپذیرید.';

        if ($errors) {
            keep_old($_POST);
            set_field_errors($errors);
            flash('error', implode('<br>', array_map('e', $errors)), 'error');
            redirect('/checkout');
        }

        $pdo = DB::pdo();
        $pdo->beginTransaction();
        try {
            // بررسی نهایی موجودی
            foreach ($cart->items() as $item) {
                $stock = (int)DB::value("SELECT stock FROM products WHERE id = ?", [$item['product']['id']]);
                if ($stock < $item['qty']) {
                    throw new \RuntimeException('موجودی محصول «' . $item['product']['name'] . '» کافی نیست.');
                }
            }

            $code = 'NS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $coupon = $cart->coupon();

            $orderId = DB::insert('orders', [
                'user_id'        => Auth::id(),
                'order_code'     => $code,
                'status'         => 'pending',
                'subtotal'       => $cart->subtotal(),
                'discount'       => $cart->couponDiscount(),
                'shipping'       => $cart->shipping(),
                'total'          => $cart->total(),
                'coupon_code'    => $coupon['code'] ?? null,
                'payment_method' => $data['payment_method'],
                'receiver_name'  => $data['receiver_name'],
                'receiver_phone' => $data['receiver_phone'],
                'city'           => $data['city'],
                'address'        => $data['address'],
                'postal_code'    => $data['postal_code'] ?: null,
                'note'           => $data['note'] ?: null,
            ]);

            foreach ($cart->items() as $item) {
                DB::insert('order_items', [
                    'order_id'      => $orderId,
                    'product_id'    => $item['product']['id'],
                    'product_name'  => $item['product']['name'],
                    'product_image' => $item['product']['image'],
                    'price'         => $item['unit'],
                    'qty'           => $item['qty'],
                ]);
                DB::query("UPDATE products SET stock = stock - ?, sold = sold + ? WHERE id = ?", [$item['qty'], $item['qty'], $item['product']['id']]);
            }

            if ($coupon) {
                DB::query("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$coupon['id']]);
            }

            // ذخیره آدرس در پروفایل کاربر برای دفعات بعد
            DB::update('users', [
                'phone'       => $data['receiver_phone'],
                'city'        => $data['city'],
                'address'     => $data['address'],
                'postal_code' => $data['postal_code'] ?: null,
            ], 'id = :id', ['id' => Auth::id()]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            flash('error', $e->getMessage(), 'error');
            redirect('/cart');
        }

        $cart->clear();

        if ($data['payment_method'] === 'cod') {
            DB::update('orders', ['status' => 'processing'], 'id = :id', ['id' => $orderId]);
            redirect('/order/success/' . $code);
        }
        redirect('/payment/' . $code);
    }

    /** درگاه پرداخت شبیه‌سازی‌شده */
    public function payment(string $code): void
    {
        $order = DB::fetch("SELECT * FROM orders WHERE order_code = ? AND user_id = ?", [$code, Auth::id()]);
        if (!$order) {
            http_response_code(404);
            view('errors/404', ['title' => 'سفارش پیدا نشد']);
            return;
        }
        if ($order['status'] !== 'pending') {
            redirect('/order/success/' . $code);
        }
        view('checkout/payment', ['title' => 'پرداخت سفارش', 'order' => $order], 'plain');
    }

    public function verify(string $code): void
    {
        abort_csrf();
        $order = DB::fetch("SELECT * FROM orders WHERE order_code = ? AND user_id = ?", [$code, Auth::id()]);
        if (!$order || $order['status'] !== 'pending') {
            redirect('/account/orders');
        }

        if (input('result') === 'success') {
            DB::update('orders', [
                'status'      => 'paid',
                'payment_ref' => \App\Core\Encrypter::encrypt('TRX' . random_int(100000, 999999)),
                'paid_at'     => date('Y-m-d H:i:s'),
            ], 'id = :id', ['id' => $order['id']]);
            redirect('/order/success/' . $code);
        }

        // پرداخت ناموفق → بازگرداندن موجودی و لغو سفارش
        $items = DB::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
        foreach ($items as $it) {
            if ($it['product_id']) {
                DB::query("UPDATE products SET stock = stock + ?, sold = sold - ? WHERE id = ?", [$it['qty'], $it['qty'], $it['product_id']]);
            }
        }
        DB::update('orders', ['status' => 'cancelled'], 'id = :id', ['id' => $order['id']]);
        flash('error', 'پرداخت ناموفق بود و سفارش لغو شد. می‌توانید دوباره تلاش کنید.', 'error');
        redirect('/account/orders/' . $code);
    }

    public function success(string $code): void
    {
        $order = DB::fetch("SELECT * FROM orders WHERE order_code = ? AND user_id = ?", [$code, Auth::id()]);
        if (!$order) {
            redirect('/account/orders');
        }
        $items = DB::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
        $isEn = !\App\Core\I18n::isRtl();
        view('checkout/success', [
            'title' => ($isEn ? 'Order Confirmed' : 'سفارش شما ثبت شد') . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => $isEn ? 'Your order has been placed successfully.' : 'سفارش شما با موفقیت ثبت شد. کد پیگیری را نگه دارید.',
            'order' => $order,
            'items' => $items
        ]);
    }

    /** پیگیری سفارش با کد */
    public function track(): void
    {
        $code  = strtoupper(trim(en_num((string)input('code', ''))));
        $phone = en_num(trim((string)input('phone', '')));
        $order = null;
        $items = [];
        $isEn  = !\App\Core\I18n::isRtl();
        if ($code !== '' && $phone !== '') {
            $order = DB::fetch("SELECT * FROM orders WHERE order_code = ? AND receiver_phone = ?", [$code, $phone]);
            if ($order) {
                $items = DB::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
            } else {
                flash('error', $isEn ? 'No order found with these details.' : 'سفارشی با این مشخصات پیدا نشد.', 'error');
            }
        }
        view('checkout/track', [
            'title' => __('track_order', 'پیگیری سفارش') . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => $isEn ? 'Track your order status with tracking code and phone number.' : 'وضعیت سفارش خود را با کد پیگیری و شماره موبایل مشاهده کنید.',
            'order' => $order,
            'items' => $items,
            'code' => $code,
            'phone' => $phone
        ]);
    }
}
