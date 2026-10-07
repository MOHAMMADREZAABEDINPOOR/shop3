<?php
namespace App\Controllers\Admin;

use App\Core\Database as DB;
use App\Core\Auth;

class UserController
{
    public function index(): void
    {
        $q = trim((string)input('q', ''));
        $params = [];
        $where = '1=1';
        if ($q !== '') {
            $where = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $params = ["%{$q}%", "%{$q}%", "%{$q}%"];
        }
        $users = DB::fetchAll(
            "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders_count,
                    (SELECT COALESCE(SUM(total),0) FROM orders o WHERE o.user_id = u.id AND o.status NOT IN ('pending','cancelled')) AS spent
             FROM users u WHERE {$where} ORDER BY u.created_at DESC",
            $params
        );
        view('admin/users', ['title' => 'مدیریت کاربران', 'users' => $users, 'q' => $q], 'admin');
    }

    public function updateRole(string $id): void
    {
        abort_csrf();
        $role = input('role') === 'admin' ? 'admin' : 'customer';
        if ((int)$id === Auth::id()) {
            flash('error', 'نمی‌توانید نقش خودتان را تغییر دهید.', 'error');
            redirect('/admin/users');
        }
        DB::update('users', ['role' => $role], 'id = :id', ['id' => (int)$id]);
        flash('success', 'نقش کاربر تغییر کرد.');
        redirect('/admin/users');
    }

    public function delete(string $id): void
    {
        abort_csrf();
        if ((int)$id === Auth::id()) {
            flash('error', 'نمی‌توانید حساب خودتان را حذف کنید.', 'error');
            redirect('/admin/users');
        }
        DB::delete('users', 'id = ?', [(int)$id]);
        flash('success', 'کاربر حذف شد.');
        redirect('/admin/users');
    }
}
