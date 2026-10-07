<?php
namespace App\Core;

/**
 * سبد خرید مبتنی بر Session با پشتیبانی از کد تخفیف
 */
class Cart
{
    private static ?Cart $instance = null;
    private ?array $itemsCache = null;

    public static function instance(): Cart
    {
        if (!self::$instance) {
            self::$instance = new Cart();
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
        }
        return self::$instance;
    }

    /** افزودن محصول */
    public function add(int $productId, int $qty = 1): array
    {
        $product = Database::fetch("SELECT * FROM products WHERE id = ? AND is_active = 1", [$productId]);
        if (!$product) {
            return ['ok' => false, 'message' => 'محصول پیدا نشد.'];
        }
        $current = $_SESSION['cart'][$productId] ?? 0;
        $newQty  = $current + max(1, $qty);
        if ($newQty > $product['stock']) {
            return ['ok' => false, 'message' => 'موجودی این محصول کافی نیست. (موجودی: ' . fa_num($product['stock']) . ' عدد)'];
        }
        $_SESSION['cart'][$productId] = $newQty;
        $this->itemsCache = null;
        return ['ok' => true, 'message' => 'محصول به سبد خرید اضافه شد.'];
    }

    /** تنظیم تعداد */
    public function update(int $productId, int $qty): array
    {
        if ($qty <= 0) {
            return $this->remove($productId);
        }
        $product = Database::fetch("SELECT stock FROM products WHERE id = ?", [$productId]);
        if (!$product) {
            return ['ok' => false, 'message' => 'محصول پیدا نشد.'];
        }
        if ($qty > $product['stock']) {
            return ['ok' => false, 'message' => 'حداکثر تعداد قابل سفارش ' . fa_num($product['stock']) . ' عدد است.'];
        }
        $_SESSION['cart'][$productId] = $qty;
        $this->itemsCache = null;
        return ['ok' => true, 'message' => 'سبد خرید به‌روزرسانی شد.'];
    }

    public function remove(int $productId): array
    {
        unset($_SESSION['cart'][$productId]);
        $this->itemsCache = null;
        return ['ok' => true, 'message' => 'محصول از سبد خرید حذف شد.'];
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
        unset($_SESSION['coupon']);
        $this->itemsCache = null;
    }

    /** آیتم‌های سبد با اطلاعات کامل محصول */
    public function items(): array
    {
        if ($this->itemsCache !== null) {
            return $this->itemsCache;
        }
        $ids = array_keys($_SESSION['cart'] ?? []);
        if (!$ids) {
            return $this->itemsCache = [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $products = Database::fetchAll("SELECT * FROM products WHERE id IN ({$placeholders})", $ids);
        $items = [];
        foreach ($products as $p) {
            $qty = (int)$_SESSION['cart'][$p['id']];
            // اگر موجودی کم شده باشد تعداد را اصلاح می‌کنیم
            if ($qty > $p['stock']) {
                $qty = max(0, (int)$p['stock']);
                if ($qty === 0) {
                    unset($_SESSION['cart'][$p['id']]);
                    continue;
                }
                $_SESSION['cart'][$p['id']] = $qty;
            }
            $unit = final_price($p);
            $items[] = [
                'product'  => $p,
                'qty'      => $qty,
                'unit'     => $unit,
                'subtotal' => $unit * $qty,
            ];
        }
        return $this->itemsCache = $items;
    }

    public function count(): int
    {
        return array_sum($_SESSION['cart'] ?? []);
    }

    public function isEmpty(): bool
    {
        return empty($_SESSION['cart']);
    }

    public function subtotal(): int
    {
        return (int)array_sum(array_column($this->items(), 'subtotal'));
    }

    /** مجموع تخفیف قیمت‌های محصولات (اختلاف قیمت اصلی و تخفیف‌دار) */
    public function productSavings(): int
    {
        $sum = 0;
        foreach ($this->items() as $item) {
            $p = $item['product'];
            if ($p['discount_price'] && $p['discount_price'] < $p['price']) {
                $sum += ($p['price'] - $p['discount_price']) * $item['qty'];
            }
        }
        return $sum;
    }

    public function shipping(): int
    {
        $subtotal = $this->subtotal() - $this->couponDiscount();
        if ($subtotal <= 0) {
            return 0;
        }
        return $subtotal >= (int)config('free_shipping_threshold') ? 0 : (int)config('shipping_cost');
    }

    // ---------- کد تخفیف ----------

    public function applyCoupon(string $code): array
    {
        $code = strtoupper(trim(en_num($code)));
        $coupon = Database::fetch("SELECT * FROM coupons WHERE code = ? AND is_active = 1", [$code]);
        if (!$coupon) {
            return ['ok' => false, 'message' => 'کد تخفیف نامعتبر است.'];
        }
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            return ['ok' => false, 'message' => 'این کد تخفیف منقضی شده است.'];
        }
        if ($coupon['max_uses'] !== null && $coupon['used_count'] >= $coupon['max_uses']) {
            return ['ok' => false, 'message' => 'سقف استفاده از این کد تخفیف به پایان رسیده است.'];
        }
        if ($this->subtotal() < (int)$coupon['min_total']) {
            return ['ok' => false, 'message' => 'حداقل مبلغ سفارش برای این کد ' . price($coupon['min_total']) . ' است.'];
        }
        $_SESSION['coupon'] = $coupon;
        return ['ok' => true, 'message' => 'کد تخفیف با موفقیت اعمال شد.'];
    }

    public function removeCoupon(): void
    {
        unset($_SESSION['coupon']);
    }

    public function coupon(): ?array
    {
        return $_SESSION['coupon'] ?? null;
    }

    public function couponDiscount(): int
    {
        $coupon = $this->coupon();
        if (!$coupon) {
            return 0;
        }
        $subtotal = $this->subtotal();
        if ($subtotal < (int)$coupon['min_total']) {
            return 0;
        }
        if ($coupon['type'] === 'percent') {
            return (int)floor($subtotal * (int)$coupon['value'] / 100);
        }
        return (int)min($subtotal, (int)$coupon['value']);
    }

    public function total(): int
    {
        return max(0, $this->subtotal() - $this->couponDiscount() + $this->shipping());
    }

    /** خلاصه سبد برای پاسخ‌های AJAX */
    public function summary(): array
    {
        return [
            'count'     => $this->count(),
            'subtotal'  => $this->subtotal(),
            'discount'  => $this->couponDiscount(),
            'shipping'  => $this->shipping(),
            'total'     => $this->total(),
            'subtotal_f' => price($this->subtotal()),
            'discount_f' => price($this->couponDiscount()),
            'shipping_f' => $this->shipping() === 0 ? 'رایگان' : price($this->shipping()),
            'total_f'    => price($this->total()),
        ];
    }
}
