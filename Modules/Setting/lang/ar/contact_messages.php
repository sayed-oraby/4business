<?php

return [
    'title' => 'رسائل اتصل بنا',
    'description' => 'إدارة رسائل التواصل الواردة من العملاء',
    
    'fields' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الجوال',
        'country_code' => 'كود الدولة',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'status' => 'الحالة',
        'created_at' => 'تاريخ الإرسال',
    ],

    'statuses' => [
        'pending' => 'قيد الانتظار',
        'read' => 'تم القراءة',
        'replied' => 'تم الرد',
        'closed' => 'مغلق',
    ],

    'messages' => [
        'created' => 'تم إرسال الرسالة بنجاح.',
        'status_updated' => 'تم تحديث حالة الرسالة.',
        'deleted' => 'تم حذف الرسالة.',
        'bulk_deleted' => 'تم حذف :count رسالة.',
    ],

    'stats' => [
        'total' => 'إجمالي الرسائل',
        'pending' => 'قيد الانتظار',
        'read' => 'تم القراءة',
        'replied' => 'تم الرد',
        'closed' => 'مغلق',
    ],

    'actions' => [
        'view' => 'عرض',
        'delete' => 'حذف',
        'update_status' => 'تحديث الحالة',
        'bulk_delete' => 'حذف المحدد',
    ],

    'confirm' => [
        'delete_title' => 'حذف الرسالة؟',
        'delete_message' => 'هل أنت متأكد من حذف هذه الرسالة؟ لا يمكن التراجع عن هذا الإجراء.',
        'bulk_title' => 'حذف الرسائل المحددة؟',
        'bulk_message' => 'أنت على وشك حذف :count رسالة. لا يمكن التراجع عن هذا الإجراء.',
    ],
];
