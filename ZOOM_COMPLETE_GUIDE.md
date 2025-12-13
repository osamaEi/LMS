# 🎓 دليل Zoom الكامل - LMS System

## ✨ الميزات المكتملة

### 1. الميزات الأساسية
- ✅ إنشاء اجتماعات Zoom تلقائياً عند إنشاء session
- ✅ تحديث اجتماعات Zoom تلقائياً عند تعديل session
- ✅ حذف اجتماعات Zoom تلقائياً عند حذف session
- ✅ الانضمام للاجتماعات مباشرة من الموقع (Zoom Web SDK)
- ✅ SDK Signature generation للأمان

### 2. التسجيل التلقائي (Auto Recording)
- ✅ تسجيل تلقائي على Zoom Cloud
- ✅ Webhook لتحميل التسجيلات تلقائياً بعد انتهاء الاجتماع
- ✅ حفظ التسجيل في ملفات الدرس تلقائياً
- ✅ عرض التسجيلات في واجهة الموقع

### 3. المحادثة والتفاعل (Chat & Interaction)
- ✅ المحادثة داخل Zoom (Chat enabled)
- ✅ الأسئلة والأجوبة (Q&A)
- ✅ الترجمة المباشرة (Closed Captions)
- ✅ مشاركة الشاشة (Screen Sharing)
- ✅ حفظ المحادثات

### 4. واجهة مستخدم مبهرة
- ✅ تصميم احترافي وعصري
- ✅ عداد تنازلي متحرك للجلسات القادمة
- ✅ تأثيرات بصرية وأنيميشن
- ✅ Dark mode support
- ✅ Responsive design

---

## 📂 هيكل الملفات

```
app/
├── Services/
│   └── ZoomService.php                    # Core Zoom service
├── Http/
│   └── Controllers/
│       ├── Admin/
│       │   ├── SessionController.php       # Auto-create/update/delete Zoom meetings
│       │   └── ZoomWebhookController.php   # Handle Zoom webhooks
│       └── Api/
│           └── V1/
│               └── Admin/
│                   └── ZoomController.php  # API endpoints
│
resources/
└── views/
    └── admin/
        └── sessions/
            ├── create.blade.php            # Simplified form (auto-creation)
            └── show.blade.php              # Impressive design with SDK integration
│
routes/
├── web.php                                # OAuth callback route
└── api.php                                # Webhook route
│
config/
└── services.php                           # Zoom credentials
│
.env                                       # Environment variables
│
database/
└── migrations/
    └── 2025_12_13_072933_add_has_recording_to_sessions_table.php
```

---

## 🔑 الإعدادات المطلوبة

### 1. Zoom Apps (اثنان منفصلان!)

#### App 1: Server-to-Server OAuth App
**الاستخدام**: للـ API (إنشاء/تعديل/حذف meetings)

**الخطوات**:
1. اذهب إلى https://marketplace.zoom.us/develop/create
2. اختر: **Server-to-Server OAuth**
3. املأ المعلومات المطلوبة
4. في **Scopes**، أضف:
   - `meeting:write:admin`
   - `meeting:read:admin`
   - `recording:read:admin`
   - `recording:write:admin`
5. احصل على:
   - Account ID
   - Client ID
   - Client Secret

#### App 2: General App (SDK App)
**الاستخدام**: للـ Web SDK (الانضمام للـ meetings)

**الخطوات**:
1. اذهب إلى https://marketplace.zoom.us/develop/create
2. اختر: **General App** (أو Meeting SDK)
3. في **Features**:
   - فعّل **Zoom App SDK**
4. في **App Credentials**، احصل على:
   - SDK Key (Client ID)
   - SDK Secret (Client Secret)
5. في **OAuth Redirect URL**: `http://127.0.0.1:8000/oauth/callback`

### 2. Webhook Setup (للتسجيلات التلقائية)

**في Server-to-Server OAuth App**:
1. اذهب إلى **Feature** → **Event Subscriptions**
2. **Webhook URL**:
   - محلياً (للتطوير): استخدم ngrok
     ```bash
     ngrok http 127.0.0.1:8000
     ```
     ثم ضع: `https://YOUR-NGROK-URL.ngrok.io/api/zoom/webhook`

   - على السيرفر (للإنتاج): `https://yourdomain.com/api/zoom/webhook`

3. **Event Types**:
   - ✅ `recording.completed`
   - ✅ `meeting.ended`

4. **Webhook Secret Token**: استخدم القيمة في `.env`

### 3. إعدادات .env

```env
# Server-to-Server OAuth (for API)
ZOOM_ACCOUNT_ID=4IiGyzxSS4qUwqT3u0VV3g
ZOOM_CLIENT_ID=c9hiCCeQQkKEVHVjGnvRKQ
ZOOM_CLIENT_SECRET=Hyx87I4EM94rA355ThJ68xeOO1Ce8GQY

# SDK App (for Web SDK)
ZOOM_SDK_KEY=ZSwh92_sSWOUjG2h3mUUZA
ZOOM_SDK_SECRET=QDTS7idx7r17yriof1ZN9o7QCxecgF49

# Webhook
ZOOM_WEBHOOK_SECRET_TOKEN=5uRZEK30Q1q5DylNsNheEw
```

---

## 🚀 سير العمل الكامل (Complete Workflow)

### 1. إنشاء Session جديدة

```
المستخدم → يفتح /admin/sessions/create
↓
يختار "Zoom Live" كنوع الدرس
↓
يملأ المعلومات (العنوان، التاريخ، المدة، الوصف)
↓
يضغط "حفظ"
↓
SessionController → store()
↓
ZoomService → createMeeting()
↓
Zoom API → إنشاء meeting جديد
↓
حفظ Session مع meeting_id و join_url
↓
✅ تم! Session جاهزة مع Zoom meeting
```

### 2. الانضمام للاجتماع

```
المستخدم → يفتح /admin/sessions/{id}
↓
يرى واجهة مبهرة مع:
  - عداد تنازلي (للجلسات القادمة)
  - زر "الانضمام للاجتماع"
  - معلومات الجلسة
↓
يضغط "الانضمام للاجتماع"
↓
JavaScript → يطلب signature من السيرفر
↓
ZoomController → generateSignature()
↓
ZoomService → يولّد JWT signature
↓
JavaScript → يستلم signature
↓
ZoomMtg.init() → يهيئ Zoom Web SDK
↓
ZoomMtg.join() → ينضم للاجتماع
↓
✅ المستخدم داخل الاجتماع!
  - يمكنه استخدام Chat
  - يمكنه مشاركة الشاشة
  - التسجيل التلقائي يعمل في الخلفية
```

### 3. التسجيل التلقائي والتحميل

```
الاجتماع يبدأ
↓
Zoom → يسجل تلقائياً على الـ cloud
↓
الاجتماع ينتهي
↓
Zoom → يعالج التسجيل (5-10 دقائق)
↓
Zoom → يرسل webhook: recording.completed
↓
ZoomWebhookController → handleWebhook()
↓
يتحقق من meeting_id
↓
يجد الـ Session المقابلة
↓
يحمّل ملف MP4 من Zoom cloud
↓
يحفظ في storage/app/public/session-files/
↓
يُنشئ SessionFile record
↓
يحدّث has_recording = true
↓
✅ التسجيل متاح في ملفات الدرس!
```

---

## 💡 الميزات المبهرة في الواجهة

### 1. عداد تنازلي متحرك (Countdown Timer)
- يظهر للجلسات المجدولة فقط
- تحديث فوري كل ثانية
- تصميم بـ gradients وأنيميشن
- رسالة "بدأت الجلسة" عند انتهاء العداد

### 2. قسم Zoom مع تأثيرات
- Gradient backgrounds متحركة
- Glass effect (backdrop blur)
- Hover effects (تأثير الرفع)
- نموذج دخول أنيق للاسم

### 3. معلومات الجلسة
- Badges ملونة للحالة
- Icons معبرة
- Dark mode support كامل
- تصميم responsive

---

## 🔧 استكشاف الأخطاء

### المشكلة 1: "Invalid signature, errorCode: 3712"

**السبب**: استخدام credentials خاطئة

**الحل**:
1. تأكد من استخدام `ZOOM_SDK_KEY` و `ZOOM_SDK_SECRET` (وليس CLIENT_ID/CLIENT_SECRET)
2. تأكد من إنشاء General App (SDK App) منفصل
3. امسح الـ cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### المشكلة 2: التسجيل لا يتم تحميله تلقائياً

**الحل**:
1. تحقق من الـ logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
2. تحقق من أن ngrok يعمل (للتطوير المحلي)
3. تحقق من Zoom App → Feature → Event Subscriptions → Event Log
4. تأكد من أن Webhook URL صحيح
5. تأكد من أن `ZOOM_WEBHOOK_SECRET_TOKEN` مطابق

### المشكلة 3: Chat لا يظهر في Zoom

**الحل**:
1. تحقق من أن `isSupportChat: true` في `ZoomMtg.init()`
2. تحقق من Browser console (F12) للأخطاء
3. تأكد من أن SDK version صحيح (3.8.10)
4. تأكد من تفعيل Chat في إعدادات Zoom account

---

## 📊 الإحصائيات والتحليلات

### عرض الجلسات مع التسجيلات

في `sessions/index.blade.php`، يمكنك إضافة:

```blade
@if($session->has_recording)
    <span class="badge badge-success">
        📹 تسجيل متوفر
    </span>
@endif
```

### تقرير التسجيلات

```php
// في DashboardController
$recordedSessions = Session::where('has_recording', true)->count();
$totalSessions = Session::count();
$recordingPercentage = ($recordedSessions / $totalSessions) * 100;
```

---

## 🎯 الخطوات التالية (اختياري)

### 1. إشعارات عند توفر التسجيل

```php
// في ZoomWebhookController → downloadRecording()
use App\Notifications\RecordingReadyNotification;

// بعد حفظ التسجيل
$session->subject->teacher->notify(new RecordingReadyNotification($session));
```

### 2. صلاحيات التسجيلات

```php
// في SessionFile model
public function canView($user) {
    // Students can only view recordings of their enrolled subjects
    if ($user->role === 'student') {
        return $this->session->subject->enrollments()
            ->where('student_id', $user->id)
            ->exists();
    }

    return true; // Admins and teachers can view all
}
```

### 3. تحميل التسجيلات بالخلفية

استخدم Queue jobs لتحميل التسجيلات الكبيرة:

```php
// Create job
php artisan make:job DownloadZoomRecording

// في ZoomWebhookController
DownloadZoomRecording::dispatch($session, $downloadUrl, $recordingType);
```

---

## 📱 اختبار الميزات

### 1. اختبار الإنشاء التلقائي
```
1. افتح http://127.0.0.1:8000/admin/sessions/create
2. اختر "Zoom Live"
3. املأ المعلومات
4. احفظ
5. ✅ يجب أن يظهر meeting_id و join_url تلقائياً
```

### 2. اختبار Web SDK
```
1. افتح http://127.0.0.1:8000/admin/sessions/{id}
2. اضغط "الانضمام للاجتماع"
3. ادخل اسمك
4. ✅ يجب أن يفتح Zoom مباشرة في المتصفح
5. ✅ يجب أن ترى أيقونة Chat في الواجهة
```

### 3. اختبار التسجيل التلقائي
```
1. انضم للاجتماع
2. سجّل فيديو قصير (30 ثانية)
3. اخرج من الاجتماع
4. انتظر 5-10 دقائق
5. تحقق من logs: tail -f storage/logs/laravel.log
6. ✅ يجب أن يظهر التسجيل في ملفات الدرس
```

### 4. اختبار العداد التنازلي
```
1. أنشئ session مجدولة في المستقبل
2. افتح صفحة عرض الدرس
3. ✅ يجب أن ترى عداد تنازلي متحرك
4. ✅ العداد يتحدث كل ثانية
```

---

## 📞 الدعم الفني

### مشاكل شائعة ✅

**س: لماذا لا يعمل Zoom في localhost?**
ج: Zoom Web SDK يعمل في localhost بدون مشاكل. لكن للـ webhooks، تحتاج ngrok.

**س: هل يمكن استخدام نفس App للـ API والـ SDK?**
ج: لا! يجب إنشاء app منفصل لكل استخدام.

**س: لماذا التسجيل لا يتم تحميله فوراً?**
ج: Zoom يأخذ 5-10 دقائق لمعالجة التسجيل بعد انتهاء الاجتماع.

**س: كيف أختبر الـ webhook محلياً?**
ج: استخدم ngrok:
```bash
ngrok http 127.0.0.1:8000
```

---

## 🎉 النتيجة النهائية

### ما تم إنجازه:

✅ **Auto Recording** - تسجيل تلقائي وتحميل تلقائي
✅ **Chat** - محادثة مفعّلة داخل Zoom
✅ **Q&A** - أسئلة وأجوبة مفعّلة
✅ **Screen Sharing** - مشاركة الشاشة مفعّلة
✅ **Webhooks** - تحميل تلقائي للتسجيلات
✅ **Countdown Timer** - عداد تنازلي مبهر
✅ **Impressive UI** - واجهة احترافية ومبهرة
✅ **Dark Mode** - دعم الوضع الليلي
✅ **Responsive** - تصميم متجاوب

---

**تاريخ الإنشاء**: 2025-12-13
**الحالة**: ✅ مكتمل وجاهز للاستخدام

**الميزات المفعّلة**:
- ✅ Auto Recording (Cloud)
- ✅ Chat (enabled for all)
- ✅ Webhook Integration
- ✅ Automatic Download & Storage
- ✅ Q&A, Closed Captions, Screen Sharing
- ✅ Countdown Timer
- ✅ Impressive Design
