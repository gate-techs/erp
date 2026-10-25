<?php
$translations = [
    // Documents Create
    "Select Document Type..." => "اختر نوع الوثيقة...",
    "Please select the document type." => "يرجى اختيار نوع الوثيقة.",
    "Document Name / Issuing Authority" => "اسم الوثيقة / جهة الإصدار",
    "e.g. Ministry of Interior, Public Authority for Civil Information..." => "مثال: وزارة الداخلية، الهيئة العامة للمعلومات المدنية...",
    "Please enter document name or issuing authority." => "يرجى إدخال اسم الوثيقة أو جهة الإصدار.",
    "Document Number or Accreditation Code" => "رقم الوثيقة أو كود الاعتماد",
    "e.g. DOC-849201" => "مثال: DOC-849201",
    "Please specify the document expiry date or mark as permanent." => "يرجى تحديد تاريخ انتهاء الوثيقة أو تحديدها كوثيقة دائمة.",
    "Please upload the document file or image." => "يرجى رفع ملف الوثيقة أو الصورة.",
    "Required Fields Missing" => "حقول مطلوبة مفقودة",
    "Please fill all mandatory fields marked in red." => "يرجى ملء جميع الحقول الإلزامية المحددة باللون الأحمر.",
    "Please correct the highlighted errors before submitting the document." => "يرجى تصحيح الأخطاء المحددة أدناه قبل تقديم الوثيقة.",
    
    // Profile Edit
    "Please enter your full name as in Civil ID." => "يرجى إدخال الاسم كاملاً كما في البطاقة المدنية.",
    "Primary phone number is required." => "رقم الهاتف الأساسي مطلوب.",
    "Please enter a valid email address." => "يرجى إدخال بريد إلكتروني صحيح.",
    "Password must be at least 6 characters." => "يجب ألا تقل كلمة المرور عن 6 خانات.",
    "Passwords do not match." => "كلمتا المرور غير متطابقتين.",
    "Please correct the highlighted fields before saving." => "يرجى تصحيح الحقول المحددة قبل الحفظ.",
    "Please correct the highlighted errors before saving changes." => "يرجى تصحيح الأخطاء المحددة قبل حفظ التغييرات.",
    "Image size exceeds 4MB limit!" => "حجم الصورة يتجاوز الحد المسموح 4 ميجابايت!",
    
    // Leaves Create
    "Please select a leave type." => "يرجى اختيار نوع الإجازة.",
    "Start date is required." => "تاريخ البداية مطلوب.",
    "End date is required." => "تاريخ النهاية مطلوب.",
    "End date cannot be earlier than start date." => "تاريخ النهاية لا يمكن أن يسبق تاريخ البداية.",
    "Please attach a medical report for sick leave." => "يرجى إرفاق تقرير طبي معتمد للإجازة المرضية.",
    "Requested leave days exceed your available annual balance." => "عدد أيام الإجازة المطلوبة يتجاوز رصيد إجازاتك السنوي المتاح.",
    "File size exceeds 5MB limit!" => "حجم الملف يتجاوز الحد الأقصى 5 ميجابايت!",
    "Please check the required fields" => "يرجى التحقق من الحقول المطلوبة",
    "Some fields contain invalid data or exceed limits." => "بعض الحقول تحتوي على بيانات غير صالحة أو تتجاوز الحدود المسموحة.",
    "Please correct the errors indicated below before submitting." => "يرجى تصحيح الأخطاء الموضحة أدناه قبل الإرسال."
];

$arJsonPath = __DIR__ . '/../lang/ar.json';
if (file_exists($arJsonPath)) {
    $existing = json_decode(file_get_contents($arJsonPath), true) ?: [];
    $updated = array_merge($translations, $existing); // Put new translations or merge
    // Ensure all keys from $translations are explicitly set
    foreach ($translations as $k => $v) {
        $updated[$k] = $v;
    }
    file_put_contents($arJsonPath, json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "SUCCESS: Added " . count($translations) . " translations to ar.json" . PHP_EOL;
} else {
    echo "ERROR: ar.json not found" . PHP_EOL;
}
