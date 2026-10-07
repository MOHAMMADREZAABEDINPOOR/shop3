<?php
namespace App\Controllers;

class CartController
{
    public function index(): void
    {
        $cart = cart();
        $isEn = !\App\Core\I18n::isRtl();
        view('cart/index', [
            'title' => __('cart', 'سبد خرید') . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => $isEn 
                ? 'Your shopping cart at ' . __('site_title', 'NextShop') . ' — Apply coupons and proceed to secure checkout.'
                : 'سبد خرید شما در ' . config('app_name') . ' — اعمال کد تخفیف و ادامه فرایند خرید امن.',
            'items' => $cart->items(),
            'cart'  => $cart,
        ]);
    }

    public function add(): void
    {
        abort_csrf();
        $productId = (int)en_num(input('product_id', 0));
        $qty       = max(1, (int)en_num(input('qty', 1)));
        $result    = cart()->add($productId, $qty);

        if (is_ajax()) {
            json_response($result + ['cart' => cart()->summary()], $result['ok'] ? 200 : 422);
        }
        flash($result['ok'] ? 'success' : 'error', $result['message'], $result['ok'] ? 'success' : 'error');
        back();
    }

    public function update(): void
    {
        abort_csrf();
        $productId = (int)en_num(input('product_id', 0));
        $qty       = (int)en_num(input('qty', 1));
        $result    = cart()->update($productId, $qty);

        if (is_ajax()) {
            $cart = cart();
            $line = null;
            foreach ($cart->items() as $item) {
                if ((int)$item['product']['id'] === $productId) {
                    $line = ['qty' => $item['qty'], 'subtotal_f' => price($item['subtotal'])];
                }
            }
            json_response($result + ['cart' => $cart->summary(), 'line' => $line], $result['ok'] ? 200 : 422);
        }
        flash($result['ok'] ? 'success' : 'error', $result['message'], $result['ok'] ? 'success' : 'error');
        redirect('/cart');
    }

    public function remove(): void
    {
        abort_csrf();
        $productId = (int)en_num(input('product_id', 0));
        $result    = cart()->remove($productId);
        if (is_ajax()) {
            json_response($result + ['cart' => cart()->summary()]);
        }
        flash('success', $result['message']);
        redirect('/cart');
    }

    public function coupon(): void
    {
        abort_csrf();
        $result = cart()->applyCoupon((string)input('code', ''));
        if (is_ajax()) {
            json_response($result + ['cart' => cart()->summary()], $result['ok'] ? 200 : 422);
        }
        flash($result['ok'] ? 'success' : 'error', $result['message'], $result['ok'] ? 'success' : 'error');
        redirect('/cart');
    }

    public function removeCoupon(): void
    {
        abort_csrf();
        cart()->removeCoupon();
        if (is_ajax()) {
            json_response(['ok' => true, 'message' => 'کد تخفیف حذف شد.', 'cart' => cart()->summary()]);
        }
        flash('success', 'کد تخفیف حذف شد.');
        redirect('/cart');
    }

    public function summary(): void
    {
        json_response(['ok' => true, 'cart' => cart()->summary()]);
    }
}
