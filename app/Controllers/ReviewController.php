<?php
namespace App\Controllers;

use App\Core\Database as DB;
use App\Core\Auth;

class ReviewController
{
    public function store(): void
    {
        abort_csrf();
        $pid     = (int)en_num(input('product_id', 0));
        $rating  = (int)en_num(input('rating', 5));
        $title   = trim((string)input('title', ''));
        $comment = trim((string)input('comment', ''));

        $product = DB::fetch("SELECT slug FROM products WHERE id = ?", [$pid]);
        if (!$product) {
            redirect('/shop');
        }
        $redirect = '/product/' . $product['slug'] . '#reviews';

        if ($rating < 1 || $rating > 5) $rating = 5;
        if (mb_strlen($comment) < 10) {
            flash('error', 'متن نظر باید حداقل ۱۰ حرف باشد.', 'error');
            redirect($redirect);
        }
        if (DB::fetch("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?", [Auth::id(), $pid])) {
            flash('error', 'شما قبلاً برای این محصول نظر ثبت کرده‌اید.', 'error');
            redirect($redirect);
        }

        DB::insert('reviews', [
            'product_id' => $pid,
            'user_id'    => Auth::id(),
            'rating'     => $rating,
            'title'      => $title ?: null,
            'comment'    => $comment,
        ]);
        self::recalculate($pid);

        flash('success', 'نظر شما با موفقیت ثبت شد. سپاس از همراهی شما.');
        redirect($redirect);
    }

    public static function recalculate(int $productId): void
    {
        $row = DB::fetch("SELECT COUNT(*) AS cnt, COALESCE(AVG(rating),0) AS avg FROM reviews WHERE product_id = ? AND is_approved = 1", [$productId]);
        DB::update('products', [
            'rating_avg'   => round((float)$row['avg'], 2),
            'rating_count' => (int)$row['cnt'],
        ], 'id = :id', ['id' => $productId]);
    }
}
