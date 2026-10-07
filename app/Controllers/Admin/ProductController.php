<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;
use App\Core\Seeder;

class ProductController
{
    public function index(): void
    {
        $q = trim((string)input('q', ''));
        $cat = (int)en_num(input('category', 0));
        $where = ['1=1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(p.name LIKE ? OR p.brand LIKE ?)';
            array_push($params, "%{$q}%", "%{$q}%");
        }
        if ($cat) {
            $where[] = 'p.category_id = ?';
            $params[] = $cat;
        }
        $products = DB::fetchAll(
            "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE " . implode(' AND ', $where) . " ORDER BY p.id DESC",
            $params
        );
        $categories = DB::fetchAll("SELECT * FROM categories ORDER BY sort_order");
        view('admin/products/index', ['title' => 'مدیریت محصولات', 'products' => $products, 'categories' => $categories, 'q' => $q, 'cat' => $cat], 'admin');
    }

    public function create(): void
    {
        view('admin/products/form', [
            'title'      => 'افزودن محصول جدید',
            'product'    => null,
            'specs'      => [],
            'categories' => DB::fetchAll("SELECT * FROM categories ORDER BY sort_order"),
        ], 'admin');
    }

    public function store(): void
    {
        abort_csrf();
        [$data, $errors] = $this->validate();
        if ($errors) {
            keep_old($_POST);
            flash('error', implode('<br>', $errors), 'error');
            redirect('/admin/products/create');
        }
        $data['slug'] = $this->uniqueSlug($data['slug']);
        $image = $this->handleImage(null, $data['name'], $data['category_id']);
        if ($image) {
            $data['image'] = $image;
        }
        $gallery = $this->handleGallery(null);
        if (!empty($gallery)) {
            $data['gallery'] = json_encode($gallery, JSON_UNESCAPED_SLASHES);
        }
        $id = DB::insert('products', $data);
        flash('success', 'محصول «' . e($data['name']) . '» با موفقیت اضافه شد.');
        redirect('/admin/products');
    }

    public function edit(string $id): void
    {
        $product = DB::fetch("SELECT * FROM products WHERE id = ?", [(int)$id]);
        if (!$product) {
            redirect('/admin/products');
        }
        view('admin/products/form', [
            'title'      => 'ویرایش محصول',
            'product'    => $product,
            'specs'      => $product['specs'] ? (json_decode($product['specs'], true) ?: []) : [],
            'categories' => DB::fetchAll("SELECT * FROM categories ORDER BY sort_order"),
        ], 'admin');
    }

    public function update(string $id): void
    {
        abort_csrf();
        $product = DB::fetch("SELECT * FROM products WHERE id = ?", [(int)$id]);
        if (!$product) {
            redirect('/admin/products');
        }
        [$data, $errors] = $this->validate();
        if ($errors) {
            keep_old($_POST);
            flash('error', implode('<br>', $errors), 'error');
            redirect('/admin/products/' . $id . '/edit');
        }
        if ($data['slug'] !== $product['slug']) {
            $data['slug'] = $this->uniqueSlug($data['slug'], (int)$id);
        }
        $image = $this->handleImage($product['image'], $data['name'], $data['category_id']);
        if ($image) {
            $data['image'] = $image;
        }
        $gallery = $this->handleGallery($product['gallery']);
        $data['gallery'] = !empty($gallery) ? json_encode($gallery, JSON_UNESCAPED_SLASHES) : null;
        DB::update('products', $data, 'id = :id', ['id' => (int)$id]);
        flash('success', 'محصول با موفقیت ویرایش شد.');
        redirect('/admin/products');
    }

    public function delete(string $id): void
    {
        abort_csrf();
        $product = DB::fetch("SELECT * FROM products WHERE id = ?", [(int)$id]);
        if ($product) {
            DB::delete('products', 'id = ?', [(int)$id]);
            if ($product['image'] && !str_starts_with($product['image'], 'http')) {
                @unlink(config('upload_dir') . '/' . $product['image']);
            }
            flash('success', 'محصول حذف شد.');
        }
        redirect('/admin/products');
    }

    public function toggle(string $id): void
    {
        abort_csrf();
        DB::query("UPDATE products SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?", [(int)$id]);
        if (is_ajax()) {
            $active = (int)DB::value("SELECT is_active FROM products WHERE id = ?", [(int)$id]);
            json_response(['ok' => true, 'active' => $active === 1, 'message' => $active ? 'محصول فعال شد.' : 'محصول غیرفعال شد.']);
        }
        redirect('/admin/products');
    }

    // ---------- helpers ----------

    private function validate(): array
    {
        $name  = trim((string)input('name', ''));
        $slug  = trim((string)input('slug', ''));
        $price = (int)en_num(str_replace(['،', ','], '', (string)input('price', '0')));
        $discount = trim((string)input('discount_price', ''));
        $discount = $discount === '' ? null : (int)en_num(str_replace(['،', ','], '', $discount));
        $stock = (int)en_num(input('stock', '0'));
        $catId = (int)en_num(input('category_id', 0));

        $errors = [];
        if (mb_strlen($name) < 3) $errors[] = 'نام محصول باید حداقل ۳ حرف باشد.';
        if ($price <= 0) $errors[] = 'قیمت باید بزرگ‌تر از صفر باشد.';
        if ($discount !== null && $discount >= $price) $errors[] = 'قیمت با تخفیف باید کمتر از قیمت اصلی باشد.';
        if ($stock < 0) $errors[] = 'موجودی نامعتبر است.';
        if (!$catId || !DB::fetch("SELECT id FROM categories WHERE id = ?", [$catId])) $errors[] = 'دسته‌بندی را انتخاب کنید.';

        // مشخصات فنی (key/value)
        $specKeys = $_POST['spec_key'] ?? [];
        $specVals = $_POST['spec_val'] ?? [];
        $specs = [];
        foreach ((array)$specKeys as $i => $k) {
            $k = trim((string)$k);
            $v = trim((string)($specVals[$i] ?? ''));
            if ($k !== '' && $v !== '') {
                $specs[$k] = $v;
            }
        }

        $data = [
            'name'              => $name,
            'slug'              => $slug !== '' ? slugify($slug) : slugify($name),
            'category_id'       => $catId,
            'brand'             => trim((string)input('brand', '')) ?: null,
            'short_description' => trim((string)input('short_description', '')) ?: null,
            'description'       => trim((string)input('description', '')) ?: null,
            'price'             => $price,
            'discount_price'    => $discount,
            'stock'             => $stock,
            'specs'             => $specs ? json_encode($specs, JSON_UNESCAPED_UNICODE) : null,
            'is_featured'       => input('is_featured') ? 1 : 0,
            'is_active'         => input('is_active') ? 1 : 0,
        ];
        $imageUrl = trim((string)input('image_url', ''));
        if ($imageUrl !== '' && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $data['image'] = $imageUrl;
        }
        return [$data, $errors];
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $row = DB::fetch("SELECT id FROM products WHERE slug = ?", [$slug]);
            if (!$row || ($ignoreId && (int)$row['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }

    /** آپلود تصویر یا تولید تصویر خودکار */
    private function handleImage(?string $current, string $name, int $catId): ?string
    {
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($_FILES['image']['tmp_name']);
            $ext   = mb_strtolower((string)pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!isset($allowed[$mime]) || $allowed[$mime] !== $ext) {
                flash('error', 'فرمت تصویر مجاز نیست (فقط jpg, png, webp, gif با پسوند مطابق).', 'error');
                return null;
            }
            if (@getimagesize($_FILES['image']['tmp_name']) === false) {
                flash('error', 'فایل آپلودشده تصویر معتبر نیست.', 'error');
                return null;
            }
            if ($_FILES['image']['size'] > 4 * 1024 * 1024) {
                flash('error', 'حجم تصویر باید کمتر از ۴ مگابایت باشد.', 'error');
                return null;
            }
            $filename = 'p-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
            $dir = config('upload_dir');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . '/' . $filename)) {
                if ($current && !str_starts_with($current, 'http')) {
                    @unlink($dir . '/' . $current);
                }
                return $filename;
            }
            return null;
        }

        // بدون تصویر → تولید تصویر خودکار
        if (!$current && empty($_POST['image_url'])) {
            $cat = DB::fetch("SELECT icon, color FROM categories WHERE id = ?", [$catId]);
            $filename = 'p-' . time() . '-' . bin2hex(random_bytes(3)) . '.svg';
            file_put_contents(config('upload_dir') . '/' . $filename, Seeder::svgImage('box', $cat['color'] ?? '#6366f1', '', $name, (int)(time() % 9)));
            return $filename;
        }
        return null;
    }

    /** مدیریت گالری تصاویر محصول */
    private function handleGallery(?string $currentJson): ?array
    {
        $gallery = $currentJson ? (json_decode($currentJson, true) ?: []) : [];

        // حذف موارد انتخابی
        $remove = (array)($_POST['remove_gallery'] ?? []);
        if (!empty($remove)) {
            $gallery = array_values(array_filter($gallery, fn($img) => !in_array($img, $remove, true)));
        }

        // آپلود تصاویر جدید گالری
        if (!empty($_FILES['gallery_files']['name']) && is_array($_FILES['gallery_files']['name'])) {
            $dir = config('upload_dir');
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);

            foreach ($_FILES['gallery_files']['name'] as $i => $origName) {
                if (isset($_FILES['gallery_files']['error'][$i]) && $_FILES['gallery_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['gallery_files']['tmp_name'][$i];
                    $mime = $finfo->file($tmpName);
                    $ext = mb_strtolower((string)pathinfo($origName, PATHINFO_EXTENSION));
                    if (isset($allowed[$mime]) && $allowed[$mime] === $ext && @getimagesize($tmpName) !== false) {
                        $filename = 'gal-' . time() . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];
                        if (move_uploaded_file($tmpName, $dir . '/' . $filename)) {
                            $gallery[] = $filename;
                        }
                    }
                }
            }
        }

        // آدرس‌های URL اضافی
        $urls = trim((string)input('gallery_urls', ''));
        if ($urls !== '') {
            foreach (preg_split('/[\r\n,]+/', $urls) as $u) {
                $u = trim($u);
                if ($u !== '' && filter_var($u, FILTER_VALIDATE_URL) && !in_array($u, $gallery, true)) {
                    $gallery[] = $u;
                }
            }
        }

        return $gallery;
    }
}
