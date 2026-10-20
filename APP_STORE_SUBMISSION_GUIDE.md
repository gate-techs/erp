# دليل الرفع والامتثال لمتجر أبل (Apple App Store Submission Guide)
## تطبيق: ALA ORBIT (منظومة الخدمة الذاتية للموظفين)

---

### 1. بيانات التطبيق الأساسية (App Store Connect Information)

| الحقل | القيمة المقترحة |
| :--- | :--- |
| **اسم التطبيق (App Name)** | `ALA ORBIT` |
| **العنوان الفرعي (Subtitle)** | `Employee Portal & Smart HR` |
| **معرف الحزمة (Bundle Identifier)** | `kw.astroleaders.alaorbit` |
| **اللغة الأساسية (Primary Language)** | العربية أو الإنجليزية (يدعم اللغتين تلقائياً) |
| **التصنيف الأساسي (Primary Category)** | `Business` (أعمال) |
| **التصنيف الثانوي (Secondary Category)** | `Productivity` (إنتاجية) |
| **الفئة العمرية (Age Rating)** | `4+` (لا يحتوي على محتوى مقيد) |
| **رابط سياسة الخصوصية (Privacy Policy URL)** | `https://edu.astroleaderskw.com/portal/privacy` |
| **رابط الدعم الفني (Support URL)** | `https://edu.astroleaderskw.com/portal/privacy` أو بريد `hr@astroleaderskw.com` |

---

### 2. مذكرات مراجعة أبل والحساب التجريبي (App Review Notes & Credentials)

> [!IMPORTANT]
> متجر أبل يرفض أي تطبيق موجه لموظفين ولا يحتوي على تسجيل دخول عام إذا لم يتم توفير حساب تجريبي وشرح لطبيعة التطبيق.
> **انسخ النص التالي وضعه مباشرة في خانة `Review Notes` داخل صفحة تقديم النسخة في App Store Connect:**

```text
Dear Apple App Review Team,

ALA ORBIT is the official internal employee self-service and human resources application for the staff and faculty of Astro Leaders Academy (State of Kuwait). 

Because account registration is managed centrally by the organization's HR administration, please use the following active demo reviewer credentials to test all features:

- Demo Username / Email: reviewer@astroleaderskw.com
- Demo Password: ReviewerPassword2026!

Key Features Available for Testing:
1. Daily Attendance & Geofenced Check-in/Check-out.
2. Leave Requests (Annual, Sick, Emergency) with real-time balance calculations.
3. Salary Details & Payslip viewing.
4. Loan & Financial Advance requests.
5. Notification Preferences & Security Settings (including biometric verification and Account Deletion Request option under Security Settings in compliance with Guideline 5.1.1(v)).

Public Privacy Policy:
https://edu.astroleaderskw.com/portal/privacy

Contact Information:
HR & IT Administration: info@astroleaderskw.com / hr@astroleaderskw.com
State of Kuwait
```

---

### 3. أذونات الخصوصية لملف `Info.plist` في Xcode

يجب التأكد من وجود هذه النصوص الدقيقة في ملف `Info.plist` حتى لا ترفض أبل التطبيق بسبب غموض أسباب طلب الصلاحيات:

```xml
<!-- 1. إذن الموقع الجغرافي أثناء استخدام التطبيق للبصمة -->
<key>NSLocationWhenInUseUsageDescription</key>
<string>ALA ORBIT uses your GPS location solely during attendance check-in to verify that you are physically present within the approved nursery campus geofence.</string>

<!-- 2. إذن الكاميرا لرفع المستندات والتقارير الطبية والصورة الشخصية -->
<key>NSCameraUsageDescription</key>
<string>ALA ORBIT requires camera access to allow you to capture profile photos, medical excuse reports, and official HR request attachments.</string>

<!-- 3. إذن مكتبة الصور -->
<key>NSPhotoLibraryUsageDescription</key>
<string>ALA ORBIT requires photo library access so you can select and attach supporting documents for leave and HR requests.</string>

<!-- 4. إذن بصمة الوجه Face ID لتأكيد العمليات الحساسة -->
<key>NSFaceIDUsageDescription</key>
<string>ALA ORBIT uses Face ID to securely authenticate your identity before modifying passwords or sensitive security settings.</string>
```

---

### 4. بنود الامتثال التي تم إنجازها في الكود (Compliance Accomplished)

1. **الامتثال للبند 5.1.1(v) - خيار حذف الحساب (Account Deletion)**:
   - تم تزويد شاشة الأمان [security-settings.blade.php](file:///d:/xamp8.2/htdocs/gate_tech/erp/Modules/EmployeePortal/resources/views/security-settings.blade.php) ببطاقة واضحة لطلب حذف الحساب ومسح البيانات مع نافذة تأكيد بكلمة المرور ومسار مخصص [route: portal.profile.security.deleteAccount].
2. **صفحة سياسة الخصوصية وشروط الاستخدام المستقلة**:
   - تم إنشاء صفحة عامة متوافقة بالكامل [privacy.blade.php](file:///d:/xamp8.2/htdocs/gate_tech/erp/Modules/EmployeePortal/resources/views/privacy.blade.php) على الرابط: `/portal/privacy` لا تشترط تسجيل الدخول، وتوضح أسباب جمع الموقع والكاميرا وتفاصيل التشفير.
3. **الامتثال للبند 4.2 - حالة انقطاع الإنترنت (Offline Handling)**:
   - التطبيق مزود بصفحة [offline.blade.php](file:///d:/xamp8.2/htdocs/gate_tech/erp/Modules/EmployeePortal/resources/views/offline.blade.php) و Service Worker لتفادي الشاشة البيضاء عند عدم توفر اتصال بالإنترنت.
4. **تحديث هوية التطبيق**:
   - تم توحيد الاسم تحت **ALA ORBIT** في كافة الشاشات والأيقونات والميتا تاج وملفات الترجمة.

---

### 5. خطوات التغليف والبناء باستخدام Capacitor (iOS Build Steps)

إذا كنت تبني المشروع لأول مرة على جهاز Mac:

```bash
# 1. تثبيت Capacitor الأساسي وحزمة iOS
npm install @capacitor/core @capacitor/cli @capacitor/ios --save

# 2. إنشاء مجلد مشروع iOS
npx cap add ios

# 3. مزامنة الإعدادات والأيقونات
npx cap sync ios

# 4. فتح المشروع في Xcode
npx cap open ios
```

#### داخل Xcode:
1. اختر فريق التطوير الخاص بك (**Signing & Capabilities -> Team**).
2. اختر جهاز العرض (Any iOS Device / Generic iOS Device).
3. من القائمة العلوية اختر **Product -> Archive**.
4. بعد اكتمال الـ Archive، اضغط **Distribute App** ثم اختر **App Store Connect** لرفع النسخة مباشرة إلى حساب المطور الخاص بكم.
