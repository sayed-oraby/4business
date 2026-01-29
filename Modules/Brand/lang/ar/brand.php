<?php

return [
    'title' => 'العلامات التجارية',
    'description' => 'إدارة العلامات التجارية وتفعيلها وإرفاق الشعارات الخاصة بها.',
    'search_placeholder' => 'ابحث في العلامات...',
    'table' => [
        'title' => 'العنوان',
        'status' => 'الحالة',
        'position' => 'ترتيب العرض',
        'updated_at' => 'آخر تحديث',
        'actions' => 'الإجراءات',
    ],
    'form' => [
        'localization' => 'الاسم المترجم',
        'title_en' => 'الاسم - EN',
        'title_ar' => 'الاسم - AR',
        'status' => 'الحالة',
        'position' => 'ترتيب العرض',
        'image' => 'الشعار',
        'image_help' => 'يفضل 500x500 بكسل. الأنواع المسموح بها *.png, *.jpg, *.jpeg, *.webp',
        'save' => 'حفظ العلامة',
    ],
    'statuses' => [
        'draft' => 'مسودة',
        'active' => 'نشطة',
        'archived' => 'مؤرشفة',
    ],
    'states' => [
        'active' => 'نشط',
        'archived' => 'مؤرشف',
    ],
    'actions' => [
        'create' => 'إضافة علامة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'bulk_delete' => 'حذف المحدد',
        'bulk_delete_confirm' => 'أنت على وشك حذف :count علامات. لا يمكن التراجع عن هذه العملية.',
        'confirm_delete' => 'هل أنت متأكد من حذف هذه العلامة؟',
        'confirm' => 'تأكيد',
        'cancel' => 'إلغاء',
    ],
    'messages' => [
        'created' => 'تم إنشاء العلامة بنجاح.',
        'updated' => 'تم تحديث العلامة بنجاح.',
        'deleted' => 'تم حذف العلامة بنجاح.',
        'bulk_deleted' => 'تم حذف العلامات المحددة بنجاح.',
        'listed' => 'تم تحميل العلامات بنجاح.',
    ],
    'audit' => [
        'created' => 'تم إنشاء العلامة ":title"',
        'updated' => 'تم تحديث العلامة ":title"',
        'deleted' => 'تم حذف العلامة ":title"',
        'bulk_deleted' => 'تم حذف العلامة ":title" (حذف جماعي)',
    ],
];
