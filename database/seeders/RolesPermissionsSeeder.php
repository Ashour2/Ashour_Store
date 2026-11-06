<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Every action the dashboard offers, grouped by the feature it belongs to.
     * Adding a feature means adding its codes here and re-running the seeder.
     *
     * group => [code => [ar, en]]
     */
    public const PERMISSIONS = [
        'warehouses' => ['warehouses.manage' => ['إدارة المستودعات', 'Manage warehouses']],
        'dashboard' => [
            'dashboard.view' => ['عرض لوحة التحكم', 'View dashboard'],
        ],
        'categories' => [
            'categories.view'   => ['عرض التصنيفات', 'View categories'],
            'categories.create' => ['إضافة تصنيف', 'Add category'],
            'categories.edit'   => ['تعديل تصنيف', 'Edit category'],
            'categories.delete' => ['حذف تصنيف', 'Delete category'],
        ],
        'products' => [
            'products.view'   => ['عرض الأدوية', 'View medicines'],
            'products.create' => ['إضافة دواء', 'Add medicine'],
            'products.edit'   => ['تعديل دواء', 'Edit medicine'],
            'products.delete' => ['حذف دواء', 'Delete medicine'],
            'products.stock'  => ['توزيع مخزون الأدوية على الصيدليات', 'Manage branch stock'],
        ],
        'pharmacies' => [
            'pharmacies.view'   => ['عرض الصيدليات', 'View pharmacies'],
            'pharmacies.create' => ['إضافة صيدلية', 'Add pharmacy'],
            'pharmacies.edit'   => ['تعديل صيدلية', 'Edit pharmacy'],
            'pharmacies.delete' => ['حذف صيدلية', 'Delete pharmacy'],
        ],
        'articles' => [
            'articles.view'   => ['عرض المقالات', 'View articles'],
            'articles.create' => ['إضافة مقال', 'Add article'],
            'articles.edit'   => ['تعديل مقال', 'Edit article'],
            'articles.delete' => ['حذف مقال', 'Delete article'],
        ],
        'consultations' => [
            'consultations.view'   => ['عرض الاستشارات', 'View consultations'],
            'consultations.reply'  => ['الرد على الاستشارات', 'Reply to consultations'],
            'consultations.delete' => ['حذف استشارة', 'Delete consultation'],
        ],
        'orders' => [
            'orders.view'   => ['عرض الطلبات', 'View orders'],
            'orders.update' => ['تحديث حالة الطلب', 'Update order status'],
            'orders.delete' => ['حذف طلب', 'Delete order'],
        ],
        'customers' => [
            'customers.view'   => ['عرض الزبائن', 'View customers'],
            'customers.delete' => ['حذف زبون', 'Delete customer'],
        ],
        'settings' => [
            'settings.manage' => ['إدارة إعدادات الموقع', 'Manage site settings'],
        ],
        'users' => [
            'users.view'   => ['عرض المستخدمين', 'View users'],
            'users.create' => ['إضافة مستخدم', 'Add user'],
            'users.edit'   => ['تعديل مستخدم', 'Edit user'],
            'users.delete' => ['حذف مستخدم', 'Delete user'],
            'roles.view'   => ['عرض الأدوار', 'View roles'],
            'roles.edit'   => ['تعديل صلاحيات الأدوار', 'Edit role permissions'],
        ],
    ];

    /**
     * What each role starts with. The owner is not listed: it always holds
     * everything. The pharmacist starts on a sensible day-to-day set that the
     * owner can widen or narrow from the roles screen.
     */
    public const ROLE_DEFAULTS = [
        Role::WAREHOUSE => ['dashboard.view'],
        Role::PHARMACIST => [
            'dashboard.view',
            'categories.view', 'categories.create', 'categories.edit',
            'products.view', 'products.create', 'products.edit', 'products.delete',
        ],
    ];

    public function run(): void
    {
        $permissions = $this->syncPermissions();

        $roles = [
            Role::SUPER_ADMIN => [['المدير العام', 'Super admin'], true],
            Role::ADMIN       => [['مدير', 'Admin'], false],
            Role::PHARMACIST  => [['صيدلي', 'Pharmacist'], false],
            Role::WAREHOUSE   => [['مستودع أدوية', 'Warehouse'], false],
        ];

        foreach ($roles as $slug => [$name, $locked]) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => json_encode(['ar' => $name[0], 'en' => $name[1]], JSON_UNESCAPED_UNICODE),
                    'is_locked' => $locked,
                ]
            );

            // Only fill a role the first time; later edits belong to the owner
            if ($role->wasRecentlyCreated) {
                $role->permissions()->sync($this->defaultsFor($slug, $permissions));
            }
        }

        $this->adoptExistingAdmins();
    }

    /** Create any permission that is missing, keeping the labels current. */
    protected function syncPermissions()
    {
        foreach (self::PERMISSIONS as $group => $codes) {
            foreach ($codes as $code => $label) {
                Permission::updateOrCreate(
                    ['code' => $code],
                    [
                        'group' => $group,
                        'name'  => json_encode(['ar' => $label[0], 'en' => $label[1]], JSON_UNESCAPED_UNICODE),
                    ]
                );
            }
        }

        return Permission::pluck('id', 'code');
    }

    /** Admin gets everything; the pharmacist gets its starter set. */
    protected function defaultsFor(string $slug, $permissions): array
    {
        if ($slug === Role::SUPER_ADMIN || $slug === Role::ADMIN) {
            return $permissions->values()->all();
        }

        return collect(self::ROLE_DEFAULTS[$slug] ?? [])
            ->map(fn ($code) => $permissions[$code] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Users who administered the site before roles existed keep their access:
     * they become the owner rather than being locked out.
     */
    protected function adoptExistingAdmins(): void
    {
        $owner = Role::where('slug', Role::SUPER_ADMIN)->first();

        User::where('type', 'admin')->whereNull('role_id')->update(['role_id' => $owner->id]);
    }
}
