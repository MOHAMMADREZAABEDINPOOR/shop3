<?php
namespace App\Controllers;

use App\Core\Database as DB;
use App\Core\Auth;

class ShopController
{
    /** لیست محصولات با فیلتر، مرتب‌سازی و صفحه‌بندی */
    public function index(?array $category = null): void
    {
        $perPage = (int)config('per_page', 12);
        $page    = max(1, (int)en_num(input('page', 1)));
        $q       = trim((string)input('q', ''));
        $sort    = input('sort', 'newest');
        $minP    = (int)en_num(input('min_price', 0));
        $maxP    = (int)en_num(input('max_price', 0));
        $brand   = trim((string)input('brand', ''));
        $onlyDiscount = input('discount') === '1';
        $onlyStock    = input('instock') === '1';
        $catSlug = $category['slug'] ?? input('category', '');

        $where  = ['p.is_active = 1'];
        $params = [];

        if ($catSlug) {
            $where[]  = 'c.slug = ?';
            $params[] = $catSlug;
        }
        if ($q !== '') {
            $where[]  = '(p.name LIKE ? OR p.short_description LIKE ? OR p.brand LIKE ?)';
            $like     = '%' . $q . '%';
            array_push($params, $like, $like, $like);
        }
        if ($minP > 0) {
            $where[]  = 'COALESCE(p.discount_price, p.price) >= ?';
            $params[] = $minP;
        }
        if ($maxP > 0) {
            $where[]  = 'COALESCE(p.discount_price, p.price) <= ?';
            $params[] = $maxP;
        }
        if ($brand !== '') {
            $where[]  = 'p.brand = ?';
            $params[] = $brand;
        }
        if ($onlyDiscount) {
            $where[] = 'p.discount_price IS NOT NULL AND p.discount_price < p.price';
        }
        if ($onlyStock) {
            $where[] = 'p.stock > 0';
        }

        $orderBy = match ($sort) {
            'cheapest'   => 'COALESCE(p.discount_price, p.price) ASC',
            'expensive'  => 'COALESCE(p.discount_price, p.price) DESC',
            'popular'    => 'p.sold DESC, p.views DESC',
            'rating'     => 'p.rating_avg DESC, p.rating_count DESC',
            'discount'   => '(p.price - COALESCE(p.discount_price, p.price)) * 1.0 / p.price DESC',
            default      => 'p.created_at DESC, p.id DESC',
        };

        $whereSql = implode(' AND ', $where);
        $total    = (int)DB::value("SELECT COUNT(*) FROM products p JOIN categories c ON c.id = p.category_id WHERE {$whereSql}", $params);
        $pages    = max(1, (int)ceil($total / $perPage));
        $page     = min($page, $pages);
        $offset   = ($page - 1) * $perPage;

        $products = DB::fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id
             WHERE {$whereSql} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $categories = DB::fetchAll("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) AS product_count FROM categories c ORDER BY sort_order");
        $brands     = DB::fetchAll("SELECT brand, COUNT(*) AS cnt FROM products WHERE is_active = 1 AND brand IS NOT NULL GROUP BY brand ORDER BY cnt DESC");
        $priceRange = DB::fetch("SELECT MIN(COALESCE(discount_price, price)) AS min_p, MAX(COALESCE(discount_price, price)) AS max_p FROM products WHERE is_active = 1");

        $shopBanner = DB::fetch("SELECT * FROM banners WHERE is_active = 1 AND position = 'shop_top' ORDER BY sort_order ASC, id ASC LIMIT 1");

        $isEn = !\App\Core\I18n::isRtl();
        $title = $category ? category_name($category) : ($q !== '' ? __('search_results_for', 'Search results for ":q"', ['q' => $q]) : __('all_products', 'All Products'));

        view('shop/index', [
            'title'      => $title . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => $isEn 
                ? $title . ' — Compare and buy authentic electronics with warranty and fast shipping at ' . __('site_title', 'NextShop') . '.'
                : $title . ' — مقایسه و خرید آنلاین با ضمانت اصالت کالا، ارسال سریع و ۷ روز مهلت بازگشت در ' . config('app_name') . '.',
            'heading'    => $title,
            'products'   => $products,
            'categories' => $categories,
            'brands'     => $brands,
            'priceRange' => $priceRange,
            'category'   => $category,
            'total'      => $total,
            'page'       => $page,
            'pages'      => $pages,
            'sort'       => $sort,
            'q'          => $q,
            'filters'    => compact('minP', 'maxP', 'brand', 'onlyDiscount', 'onlyStock', 'catSlug'),
            'shopBanner' => $shopBanner,
        ]);
    }

    public function category(string $slug): void
    {
        $category = DB::fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
        if (!$category) {
            http_response_code(404);
            view('errors/404', ['title' => 'دسته‌بندی پیدا نشد']);
            return;
        }
        $this->index($category);
    }

    public function search(): void
    {
        $this->index();
    }

    /** صفحه محصول */
    public function show(string $slug): void
    {
        $product = DB::fetch(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.is_active = 1",
            [$slug]
        );
        if (!$product) {
            http_response_code(404);
            view('errors/404', ['title' => 'محصول پیدا نشد']);
            return;
        }

        DB::query("UPDATE products SET views = views + 1 WHERE id = ?", [$product['id']]);

        $reviews = DB::fetchAll(
            "SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC",
            [$product['id']]
        );
        $ratingDist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $r) {
            $ratingDist[(int)$r['rating']]++;
        }

        $related = DB::fetchAll(
            "SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY " . sql_random() . " LIMIT 4",
            [$product['category_id'], $product['id']]
        );

        $inWishlist = false;
        $canReview  = false;
        $hasReviewed = false;
        if (Auth::check()) {
            $inWishlist = (bool)DB::value("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?", [Auth::id(), $product['id']]);
            $hasReviewed = (bool)DB::value("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND product_id = ?", [Auth::id(), $product['id']]);
            $canReview = !$hasReviewed;
        }

        $specs = $product['specs'] ? (json_decode($product['specs'], true) ?: []) : [];

        $isEn = !\App\Core\I18n::isRtl();
        $pName = product_name($product);
        $pShort = product_short($product);
        view('shop/show', [
            'title'       => $pName . ' | ' . __('site_title', 'NextShop'),
            'meta_description' => excerpt($pShort ?: $pName, 150) . ($isEn ? ' — Authentic purchase with warranty and fast shipping.' : ' — خرید با ضمانت اصالت و ارسال سریع.'),
            'og_image'    => product_image($product['image']),
            'sticky_cta'  => \App\Core\View::capture('partials/sticky-cta', ['product' => $product]),
            'product'     => $product,
            'specs'       => $specs,
            'reviews'     => $reviews,
            'ratingDist'  => $ratingDist,
            'related'     => $related,
            'inWishlist'  => $inWishlist,
            'canReview'   => $canReview,
            'hasReviewed' => $hasReviewed,
            'inCartQty'   => $_SESSION['cart'][$product['id']] ?? 0,
        ]);
    }

    /** جستجوی زنده (AJAX) */
    public function apiSearch(): void
    {
        $q = trim((string)input('q', ''));
        if (mb_strlen($q) < 2) {
            json_response(['ok' => true, 'items' => []]);
        }
        $like = '%' . $q . '%';
        $items = DB::fetchAll(
            "SELECT p.name, p.slug, p.image, p.price, p.discount_price, c.name AS category FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND (p.name LIKE ? OR p.brand LIKE ?) ORDER BY p.sold DESC LIMIT 6",
            [$like, $like]
        );
        $out = array_map(fn($p) => [
            'name'     => $p['name'],
            'url'      => '/product/' . $p['slug'],
            'image'    => product_image($p['image']),
            'category' => $p['category'],
            'price'    => price(final_price($p)),
        ], $items);
        json_response(['ok' => true, 'items' => $out]);
    }
}
