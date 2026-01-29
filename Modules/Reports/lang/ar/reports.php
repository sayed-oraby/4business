<?php

return [
    'title' => 'التقارير والتحليلات',
    'description' => 'رؤى شاملة للإعلانات الوظيفية والأداء',
    
    // General
    'status' => 'الحالة',
    'count' => 'العدد',
    'category' => 'القسم',
    'city' => 'المدينة',
    'user' => 'المستخدم',
    'posts' => 'إعلانات',
    'posts_count' => 'عدد الإعلانات',
    'no_data' => 'لا توجد بيانات متاحة',
    'view_details' => 'عرض التفاصيل',
    'total_revenue' => 'إجمالي الإيرادات',
    'all_registered_members' => 'جميع الأعضاء المسجلين',
    'average_revenue_per_user' => 'متوسط الإيرادات لكل مستخدم',
    'back_to_reports' => 'العودة إلى التقارير',
    
    // Statuses
    'statuses' => [
        'pending' => 'قيد المراجعة',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'expired' => 'منتهي',
        'accepted' => 'مقبول',
        'awaiting_payment' => 'بانتظار الدفع',
        'payment_failed' => 'فشل الدفع',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ],
    
    // Reports sections
    'reports' => [
        // Posts Report
        'posts' => [
            'title' => 'تحليلات الإعلانات',
            'description' => 'إحصائيات ورؤى تفصيلية لجميع الإعلانات الوظيفية',
            'total' => 'إجمالي الإعلانات',
            'active' => 'الإعلانات النشطة',
            'pending' => 'الإعلانات قيد المراجعة',
            'expired' => 'الإعلانات المنتهية',
            'featured' => 'الإعلانات المميزة',
            'by_status' => 'الإعلانات حسب الحالة',
            'status_breakdown' => 'التوزيع حسب حالة الإعلان',
            'by_category' => 'الإعلانات حسب القسم',
            'category_distribution' => 'التوزيع عبر الأقسام',
            'post_types' => 'أنواع الإعلانات',
            'featured_vs_regular' => 'مقارنة المميزة والعادية',
            'regular_posts' => 'الإعلانات العادية',
            'standard_listings' => 'قوائم الوظائف القياسية',
            'featured_posts' => 'الإعلانات المميزة',
            'premium_listings' => 'القوائم المميزة المبرزة',
            'top_cities' => 'أكثر المدن',
            'geographic_distribution' => 'الإعلانات حسب الموقع',
            'most_active_users' => 'أكثر المستخدمين نشاطاً',
            'top_contributors' => 'أفضل المستخدمين حسب الإعلانات المنشأة',
        ],
        
        // Job Offers Report
        'job_offers' => [
            'title' => 'تحليلات عروض العمل',
            'description' => 'تتبع عروض العمل ومعدلات القبول',
            'total' => 'إجمالي عروض العمل',
            'acceptance_rate' => 'معدل القبول',
            'avg_salary' => 'متوسط ​​الراتب',
            'pending' => 'العروض قيد الانتظار',
            'by_status' => 'العروض حسب الحالة',
            'status_breakdown' => 'التوزيع حسب حالة العرض',
            'top_employers' => 'أفضل أصحاب العمل',
            'employers_description' => 'المستخدمون الذين أرسلوا أكثر العروض',
            'offers_sent' => 'العروض المرسلة',
            'popular_posts' => 'الإعلانات الشائعة',
            'posts_description' => 'الإعلانات التي تلقت أكثر العروض',
            'post_title' => 'عنوان الإعلان',
            'post_owner' => 'صاحب الإعلان',
            'offers_received' => 'العروض المستلمة',
        ],
        
        // Members Report
        'members' => [
            'title' => 'تحليلات الأعضاء',
            'description' => 'إحصائيات التسجيل والنمو للمستخدمين',
            'total' => 'إجمالي الأعضاء',
            'new' => 'التسجيلات الجديدة',
            'active' => 'الأعضاء النشطون',
            'growth_rate' => 'معدل النمو',
            'registrations_over_time' => 'التسجيلات عبر الوقت',
            'timeline_description' => 'الجدول الزمني لتسجيلات المستخدمين',
            'period' => 'الفترة',
            'registrations' => 'التسجيلات',
        ],
        
        // Financial Report
        'financial' => [
            'title' => 'تحليلات الإيرادات',
            'description' => 'إيرادات الباقات والرؤى المالية',
            'total_revenue' => 'إجمالي الإيرادات',
            'paid_posts' => 'الإعلانات المدفوعة',
            'arpu' => 'متوسط ​​الإيرادات/مستخدم',
            'by_package' => 'الإيرادات حسب الباقة',
            'package_breakdown' => 'توزيع الإيرادات حسب الباقة',
            'package_name' => 'اسم الباقة',
            'sales' => 'المبيعات',
            'revenue' => 'الإيرادات',
            'top_packages' => 'أفضل الباقات أداءً',
            'best_sellers' => 'الباقات الأكثر مبيعاً',
        ],
    ],
];
