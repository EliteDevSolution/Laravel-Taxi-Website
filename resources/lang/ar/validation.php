<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'يجب قبول السمة.',
    'active_url'           => 'السمة: ليست عنوان URL صالحًا.',
    'after'                => 'يجب أن تكون السمة: تاريخًا بعد: التاريخ.',
    'after_or_equal'       => 'يجب أن تكون السمة: تاريخًا بعد أو يساوي: التاريخ.',
    'alpha'                => 'قد تحتوي السمة: على أحرف فقط.',
    'alpha_dash'           => 'قد تحتوي السمة: على أحرف وأرقام وشرطات فقط.',
    'alpha_num'            => 'قد تحتوي السمة: على أحرف وأرقام فقط.',
    'array'                => 'يجب أن تكون السمة: مصفوفة.',
    'before'               => 'ال :attribute يجب أن يكون تاريخ من قبل :date.',
    'before_or_equal'      => 'ال :attribute يجب أن يكون تاريخًا قبل أو يساوي :date.',
    'between'              => [
        'numeric' => 'ال :attribute يجب ان يكون بين :min و :max.',
        'file'    => 'ال :attribute يجب ان يكون بين :min و :max kilobytes.',
        'string'  => 'ال :attribute يجب ان يكون بين :min و :max characters.',
        'array'   => 'ال :attribute يجب أن يكون بين :min و :max items.',
    ],
    'boolean'              => 'يجب أن يكون حقل السمة: true أو false.',
    'confirmed'            => 'لا يتطابق تأكيد السمة.',
    'date'                 => 'السمة: ليست تاريخًا صالحًا.',
    'date_format'          => 'السمة: لا تتطابق مع التنسيق: format.',
    'different'            => 'يجب أن تكون السمة و: أخرى مختلفة.',
    'digits'               => 'يجب أن تكون السمة: أرقام أرقام.',
    'digits_between'       => 'يجب أن تتراوح السمة: بين: min و: max numbers.',
    'dimensions'           => 'تحتوي السمة: على أبعاد صور غير صالحة.',
    'distinct'             => 'يحتوي حقل السمة على قيمة مكررة.',
    'email'                => 'يجب أن تكون السمة: عنوان بريد إلكتروني صالحًا.',
    'exists'               => 'السمة المحددة: غير صالحة.',
    'file'                 => 'السمة المحددة: غير صالحة',
    'filled'               => 'حقل السمة مطلوب.',
    'image'                => 'يجب أن تكون السمة: صورة.',
    'in'                   => 'السمة المحددة: غير صالحة.',
    'in_array'             => 'حقل السمة: غير موجود في: other.',
    'integer'              => 'يجب أن تكون الخاصية المميزة: عدد صحيح.',
    'ip'                   => 'يجب أن تكون السمة: عنوان IP صالحًا.',
    'json'                 => 'يجب أن تكون السمة: عبارة عن سلسلة JSON صالحة.',
    'max'                  => [
        'numeric' => 'يجب أن تكون السمة: عبارة عن سلسلة JSON صالحة',
        'file'    => 'قد لا تكون السمة: أكبر من: max kilobytes.',
        'string'  => 'قد لا تكون السمة: أكبر من: max characters.',
        'array'   => 'قد لا تحتوي السمة: على أكثر من: الحد الأقصى للعناصر.',
    ],
    'mimes'                => 'يجب أن تكون السمة: ملف من النوع:: القيم.',
    'mimetypes'            => 'يجب أن تكون السمة: ملف من النوع:: القيم.',
    'min'                  => [
        'numeric' => 'لا :attribute لا بد أن يكون على الأقل :min.',
        'file'    => 'لا :attribute لا بد أن يكون على الأقل :min kilobytes.',
        'string'  => 'لا :attribute لا بد أن يكون على الأقل :min characters.',
        'array'   => 'لا :attribute لا بد أن يكون على الأقل :min items.',
    ],
    'not_in'               => 'السمة المحددة: غير صالحة.',
    'numeric'              => 'يجب أن تكون السمة: رقمًا.',
    'present'              => 'يجب أن يكون حقل السمة موجودًا.',
    'regex'                => 'شكل السمة: غير صالح.',
    'required'             => 'حقل السمة مطلوب.',
    'required_if'          => 'حقل السمة مطلوب عندما: الآخر هو: القيمة.',
    'required_unless'      => 'حقل السمة مطلوب ما لم: الآخر في: القيم.',
    'required_with'        => 'حقل السمة مطلوب عندما: تكون القيم موجودة.',
    'required_with_all'    => 'حقل السمة مطلوب عندما: تكون القيم موجودة.',
    'required_without'     => 'حقل السمة مطلوب عندما: تكون القيم prThe: حقل السمة مطلوب عندما: تكون القيم غير موجودة.',
    'required_without_all' => 'حقل السمة مطلوب عند عدم وجود أي من: القيم.',
    'same'                 => 'يجب أن تتطابق السمة و: other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون السمة: الحجم.',
        'file'    => 'يجب أن تكون السمة: حجم الكيلوبايت.',
        'string'  => 'السمة يجب أن تكون: حجم الحروف.',
        'array'   => 'يجب أن تحتوي السمة: على عناصر الحجم.',
    ],
    'string'               => 'يجب أن تكون السمة: سلسلة.',
    'timezone'             => 'يجب أن تكون السمة: منطقة صالحة.',
    'unique'               => 'لقد تم بالفعل اتخاذ الخاصية المميزة:.',
    'uploaded'             => 'أخفق تحميل السمة.',
    'url'                  => 'لا :attribute التنسيق غير صالح.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'custom' => [
        's_latitude' => [
            'required' => 'عنوان المصدر مطلوب',
        ],
        'd_latitude' => [
            'required' => 'عنوان الوجهة مطلوب',
        ],
    ],

];
