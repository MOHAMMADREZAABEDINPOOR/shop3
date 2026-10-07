<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;

class CategoryController
{
    public function index(): void
    {
        $categories = DB::fetchAll("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY sort_order, id");
        view('admin/categories', ['title' => 'مدیریت دسته‌بندی‌ها', 'categories' => $categories], 'admin');
    }

    public function store(): void
    {
        abort_csrf();
        $data = $this->data();
        if (mb_strlen($data['name']) < 2) {
            flash('error', 'نام دسته‌بندی را وارد کنید.', 'error');
            redirect('/admin/categories');
        }
        if (DB::fetch("SELECT id FROM categories WHERE slug = ?", [$data['slug']])) {
            $data['slug'] .= '-' . random_int(10, 99);
        }
        DB::insert('categories', $data);
        flash('success', 'دسته‌بندی جدید اضافه شد.');
        redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        abort_csrf();
        $data = $this->data();
        if (mb_strlen($data['name']) < 2) {
            flash('error', 'نام دسته‌بندی را وارد کنید.', 'error');
            redirect('/admin/categories');
        }
        $dup = DB::fetch("SELECT id FROM categories WHERE slug = ? AND id != ?", [$data['slug'], (int)$id]);
        if ($dup) {
            $data['slug'] .= '-' . random_int(10, 99);
        }
        DB::update('categories', $data, 'id = :id', ['id' => (int)$id]);
        flash('success', 'دسته‌بندی ویرایش شد.');
        redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        abort_csrf();
        $count = (int)DB::value("SELECT COUNT(*) FROM products WHERE category_id = ?", [(int)$id]);
        if ($count > 0) {
            flash('error', 'این دسته‌بندی ' . fa_num($count) . ' محصول دارد. ابتدا محصولات را منتقل یا حذف کنید.', 'error');
            redirect('/admin/categories');
        }
        DB::delete('categories', 'id = ?', [(int)$id]);
        flash('success', 'دسته‌بندی حذف شد.');
        redirect('/admin/categories');
    }

    private function data(): array
    {
        $name = trim((string)input('name', ''));
        $slug = trim((string)input('slug', ''));
        $color = trim((string)input('color', '#6366f1'));
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $color = '#6366f1';
        }
        return [
            'name'        => $name,
            'slug'        => slugify($slug !== '' ? $slug : $name),
            'icon'        => trim((string)input('icon', 'box')) ?: 'box',
            'color'       => $color,
            'description' => trim((string)input('description', '')) ?: null,
            'sort_order'  => (int)en_num(input('sort_order', 0)),
        ];
    }
}
