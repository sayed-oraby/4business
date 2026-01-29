<?php

return [
    'title' => 'الأدوار والصلاحيات',
    'description' => 'تحكم في وصول المشرفين إلى وحدات النظام.',
    'roles' => [
        'table_title' => 'الأدوار',
        'name' => 'اسم الدور',
        'guard' => 'الحارس',
        'users' => 'عدد المستخدمين',
        'create' => 'إضافة دور',
        'edit' => 'تعديل دور',
    ],
    'permissions' => [
        'title' => 'الصلاحيات',
        'create' => 'إضافة صلاحية',
        'name' => 'الصلاحية',
        'module' => 'الوحدة',
        'ability' => 'العملية',
    ],
    'actions' => [
        'save' => 'حفظ',
        'delete' => 'حذف',
    ],
    'confirm' => [
        'delete_title' => 'حذف الدور؟',
        'delete_message' => 'سيتم إزالة هذا الدور وجميع صلاحياته.',
        'confirm' => 'نعم، احذف',
        'cancel' => 'إلغاء',
    ],
    'messages' => [
        'role_created' => 'تم إنشاء الدور بنجاح.',
        'role_updated' => 'تم تحديث الدور.',
        'role_deleted' => 'تم حذف الدور.',
        'permissions_synced' => 'تم تحديث الصلاحيات.',
        'permission_created' => 'تم إنشاء الصلاحية.',
        'cannot_delete_super_admin' => 'لا يمكن حذف دور المشرف العام.',
    ],
    'permissions_list' => [
        'authorization' => [
            'authorization.update' => 'إدارة الأدوار والصلاحيات',
            'authorization.view' => 'عرض الأدوار والصلاحيات',
        ],
        'dashboard' => [
            'dashboard.access' => 'الوصول إلى لوحة التحكم',
        ],
        'settings' => [
            'settings.update' => 'تحديث إعدادات النظام',
            'settings.view' => 'عرض إعدادات النظام',
        ],
        'users' => [
            'users.create' => 'إضافة مستخدمين',
            'users.delete' => 'حذف/أرشفة المستخدمين',
            'users.restore' => 'استعادة المستخدمين',
            'users.update' => 'تعديل بيانات المستخدمين',
            'users.view' => 'عرض المستخدمين',
        ],
    ],
];
