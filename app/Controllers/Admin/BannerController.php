<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;

class BannerController
{
    public function index(): void
    {
        $pos = trim((string)input('position', ''));
        $where = ['1=1'];
        $params = [];
        if ($pos !== '') {
            $where[] = 'position = ?';
            $params[] = $pos;
        }

        $banners = DB::fetchAll(
            "SELECT * FROM banners WHERE " . implode(' AND ', $where) . " ORDER BY position ASC, sort_order ASC, id DESC",
            $params
        );

        $positions = [
            'home_hero'       => 'اسلایدر هیرو (بالای صفحه اصلی)',
            'home_promo_grid' => 'شبکه سه‌تایی بنرهای ویژه (صفحه اصلی)',
            'home_middle'     => 'بنر عریض میانی (کد تخفیف / پیشنهاد بزرگ)',
            'home_dual'       => 'بنرهای دوتایی جانبی (صفحه اصلی)',
            'home_bottom'     => 'بنر پانوراما پایین صفحه (قبل از فوتر)',
            'shop_top'        => 'بنر سربرگ صفحه فروشگاه',
        ];

        view('admin/banners/index', [
            'title'     => 'مدیریت پوسترها و بنرها',
            'banners'   => $banners,
            'positions' => $positions,
            'pos'       => $pos,
        ], 'admin');
    }

    public function create(): void
    {
        view('admin/banners/form', [
            'title'     => 'افزودن پوستر جدید',
            'banner'    => null,
            'positions' => $this->positionsList(),
        ], 'admin');
    }

    public function store(): void
    {
        abort_csrf();
        [$data, $errors] = $this->validate();
        if ($errors) {
            keep_old($_POST);
            flash('error', implode('<br>', $errors), 'error');
            redirect('/admin/banners/create');
        }

        $image = $this->handleImage(null);
        if (!$image) {
            keep_old($_POST);
            flash('error', 'تصویر پوستر را آپلود کنید یا آدرس آن را وارد نمایید.', 'error');
            redirect('/admin/banners/create');
        }
        $data['image'] = $image;

        DB::insert('banners', $data);
        flash('success', 'پوستر با موفقیت ایجاد شد.');
        redirect('/admin/banners');
    }

    public function edit(string $id): void
    {
        $banner = DB::fetch("SELECT * FROM banners WHERE id = ?", [(int)$id]);
        if (!$banner) {
            flash('error', 'پوستر یافت نشد.', 'error');
            redirect('/admin/banners');
        }

        view('admin/banners/form', [
            'title'     => 'ویرایش پوستر',
            'banner'    => $banner,
            'positions' => $this->positionsList(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        abort_csrf();
        $banner = DB::fetch("SELECT * FROM banners WHERE id = ?", [(int)$id]);
        if (!$banner) {
            redirect('/admin/banners');
        }

        [$data, $errors] = $this->validate();
        if ($errors) {
            keep_old($_POST);
            flash('error', implode('<br>', $errors), 'error');
            redirect('/admin/banners/' . $id . '/edit');
        }

        $image = $this->handleImage($banner['image']);
        if ($image) {
            $data['image'] = $image;
        }

        DB::update('banners', $data, 'id = :id', ['id' => (int)$id]);
        flash('success', 'پوستر با موفقیت ویرایش شد.');
        redirect('/admin/banners');
    }

    public function delete(string $id): void
    {
        abort_csrf();
        $banner = DB::fetch("SELECT * FROM banners WHERE id = ?", [(int)$id]);
        if ($banner) {
            DB::delete('banners', 'id = ?', [(int)$id]);
            if ($banner['image'] && !str_starts_with($banner['image'], 'http')) {
                $dir = __DIR__ . '/../../../public/uploads/banners';
                @unlink($dir . '/' . $banner['image']);
            }
            flash('success', 'پوستر با موفقیت حذف شد.');
        }
        redirect('/admin/banners');
    }

    public function toggle(string $id): void
    {
        abort_csrf();
        DB::query("UPDATE banners SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?", [(int)$id]);
        if (is_ajax()) {
            $active = (int)DB::value("SELECT is_active FROM banners WHERE id = ?", [(int)$id]);
            json_response(['ok' => true, 'active' => $active === 1, 'message' => $active ? 'پوستر فعال شد.' : 'پوستر غیرفعال شد.']);
        }
        redirect('/admin/banners');
    }

    // ---------- Helpers ----------

    private function positionsList(): array
    {
        return [
            'home_hero'       => 'اسلایدر هیرو (بالای صفحه اصلی)',
            'home_promo_grid' => 'شبکه سه‌تایی بنرهای ویژه (صفحه اصلی)',
            'home_middle'     => 'بنر عریض میانی (کد تخفیف / پیشنهاد بزرگ)',
            'home_dual'       => 'بنرهای دوتایی جانبی (صفحه اصلی)',
            'home_bottom'     => 'بنر پانوراما پایین صفحه (قبل از فوتر)',
            'shop_top'        => 'بنر سربرگ صفحه فروشگاه',
        ];
    }

    private function validate(): array
    {
        $title       = trim((string)input('title', ''));
        $titleEn     = trim((string)input('title_en', '')) ?: $title;
        $subtitle    = trim((string)input('subtitle', ''));
        $subtitleEn  = trim((string)input('subtitle_en', '')) ?: $subtitle;
        $badge       = trim((string)input('badge', ''));
        $badgeEn     = trim((string)input('badge_en', '')) ?: $badge;
        $link        = trim((string)input('link', '/shop')) ?: '/shop';
        $buttonText  = trim((string)input('button_text', 'مشاهده و خرید')) ?: 'مشاهده و خرید';
        $buttonTextEn= trim((string)input('button_text_en', 'Shop Now')) ?: 'Shop Now';
        $position    = trim((string)input('position', 'home_hero'));
        $color       = trim((string)input('color', 'gradient-purple'));
        $sortOrder   = (int)en_num(input('sort_order', '0'));
        $isActive    = input('is_active') ? 1 : 0;

        $errors = [];
        if (mb_strlen($title) < 2) {
            $errors[] = 'عنوان پوستر باید حداقل ۲ حرف باشد.';
        }
        if (!array_key_exists($position, $this->positionsList())) {
            $errors[] = 'جایگاه پوستر نامعتبر است.';
        }

        $data = [
            'title'          => $title,
            'title_en'       => $titleEn,
            'subtitle'       => $subtitle ?: null,
            'subtitle_en'    => $subtitleEn ?: null,
            'badge'          => $badge ?: null,
            'badge_en'       => $badgeEn ?: null,
            'link'           => $link,
            'button_text'    => $buttonText,
            'button_text_en' => $buttonTextEn,
            'position'       => $position,
            'color'          => $color,
            'sort_order'     => $sortOrder,
            'is_active'      => $isActive,
        ];

        return [$data, $errors];
    }

    private function handleImage(?string $current): ?string
    {
        // فایل آپلودی
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/svg+xml' => 'svg'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($_FILES['image']['tmp_name']);
            $ext   = mb_strtolower((string)pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            $dir = __DIR__ . '/../../../public/uploads/banners';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $filename = 'b-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . ($allowed[$mime] ?? $ext);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . '/' . $filename)) {
                if ($current && !str_starts_with($current, 'http')) {
                    @unlink($dir . '/' . $current);
                }
                return $filename;
            }
        }

        // آدرس URL اینترنتی
        $imageUrl = trim((string)input('image_url', ''));
        if ($imageUrl !== '' && (filter_var($imageUrl, FILTER_VALIDATE_URL) || str_starts_with($imageUrl, '/'))) {
            return $imageUrl;
        }

        return $current;
    }
}
