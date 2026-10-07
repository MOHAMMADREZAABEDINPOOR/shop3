<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;

class CouponController
{
    public function index(): void
    {
        $coupons = DB::fetchAll("SELECT * FROM coupons ORDER BY id DESC");
        view('admin/coupons', ['title' => 'کدهای تخفیف', 'coupons' => $coupons], 'admin');
    }

    public function store(): void
    {
        abort_csrf();
        $code  = strtoupper(trim(en_num((string)input('code', ''))));
        $type  = input('type') === 'fixed' ? 'fixed' : 'percent';
        $value = (int)en_num(str_replace(['،', ','], '', (string)input('value', '0')));
        $min   = (int)en_num(str_replace(['،', ','], '', (string)input('min_total', '0')));
        $max   = trim((string)input('max_uses', ''));
        $exp   = trim((string)input('expires_at', ''));

        $errors = [];
        if (!preg_match('/^[A-Z0-9_-]{3,30}$/', $code)) $errors[] = 'کد تخفیف باید ۳ تا ۳۰ کاراکتر انگلیسی/عدد باشد.';
        if ($value <= 0) $errors[] = 'مقدار تخفیف نامعتبر است.';
        if ($type === 'percent' && $value > 100) $errors[] = 'درصد تخفیف نمی‌تواند بیشتر از ۱۰۰ باشد.';
        if (!$errors && DB::fetch("SELECT id FROM coupons WHERE code = ?", [$code])) $errors[] = 'این کد قبلاً ثبت شده است.';

        if ($errors) {
            flash('error', implode('<br>', $errors), 'error');
            redirect('/admin/coupons');
        }

        DB::insert('coupons', [
            'code'       => $code,
            'type'       => $type,
            'value'      => $value,
            'min_total'  => $min,
            'max_uses'   => $max === '' ? null : (int)en_num($max),
            'expires_at' => $exp === '' ? null : $exp,
            'is_active'  => 1,
        ]);
        flash('success', 'کد تخفیف «' . $code . '» ایجاد شد.');
        redirect('/admin/coupons');
    }

    public function toggle(string $id): void
    {
        abort_csrf();
        DB::query("UPDATE coupons SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?", [(int)$id]);
        flash('success', 'وضعیت کد تخفیف تغییر کرد.');
        redirect('/admin/coupons');
    }

    public function delete(string $id): void
    {
        abort_csrf();
        DB::delete('coupons', 'id = ?', [(int)$id]);
        flash('success', 'کد تخفیف حذف شد.');
        redirect('/admin/coupons');
    }
}
