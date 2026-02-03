<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => 'يجب أن يكون :attribute بريداً إلكترونياً صالحاً.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'confirmed' => 'حقل :attribute غير مطابق للتأكيد.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'min' => [
        'string' => 'يجب ألا يقل :attribute عن :min حروف.',
        'array' => 'يجب ألا يقل :attribute عن :min عناصر.',
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'file' => 'يجب ألا يقل :attribute عن :min كيلوبايت.',
    ],
    'max' => [
        'string' => 'يجب ألا يزيد :attribute عن :max حروف.',
        'array' => 'يجب ألا يزيد :attribute عن :max عناصر.',
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
        'file' => 'يجب ألا يزيد :attribute عن :max كيلوبايت.',
    ],
    'date' => 'يجب أن يكون :attribute تاريخاً صحيحاً.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'string' => 'يجب أن يكون :attribute نصاً.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'exists' => 'الخيار المحدد في :attribute غير صالح.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'current_password' => 'كلمة المرور الحالية غير صحيحة.',

    'attributes' => [
        'name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'mobile' => 'رقم الجوال',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'birthdate' => 'تاريخ الميلاد',
        'gender' => 'الجنس',
        'roles' => 'الأدوار',
        'roles.*' => 'الأدوار',
        'avatar' => 'الصورة الشخصية',
    ],
];
