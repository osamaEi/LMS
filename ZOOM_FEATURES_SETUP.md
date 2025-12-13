# 🎬 Zoom Auto Recording & Chat - دليل الإعداد الكامل

## ✨ الميزات المفعّلة

### 1️⃣ التسجيل التلقائي (Auto Recording)
- ✅ تسجيل تلقائي على الـ cloud لكل اجتماع
- ✅ تحميل التسجيل تلقائياً بعد انتهاء الاجتماع
- ✅ حفظ التسجيل في ملفات الدرس تلقائياً

### 2️⃣ المحادثة (Chat)
- ✅ المحادثة مفعّلة داخل Zoom
- ✅ يمكن لجميع المشاركين المحادثة مع الجميع
- ✅ يمكن حفظ المحادثات

### 3️⃣ ميزات إضافية
- ✅ مشاركة الشاشة (Screen Sharing)
- ✅ الأسئلة والأجوبة (Q&A)
- ✅ الترجمة المباشرة (Closed Captions)

---

## 🔧 التحديثات المطبقة

### 1. ZoomService.php
**الموقع**: `app/Services/ZoomService.php`

**التحديثات**:
```php
'settings' => [
    'auto_recording' => 'cloud',              // تسجيل تلقائي على الـ cloud
    'waiting_room' => false,                   // غرفة الانتظار معطلة للوصول السريع
    'meeting_chat' => true,                    // تفعيل المحادثة
    'allow_participants_chat_with' => 2,      // الكل يمكنه المحادثة مع الكل
    'allow_participants_save_chats' => true,  // السماح بحفظ المحادثات
],
```

### 2. show.blade.php (Zoom Web SDK)
**الموقع**: `resources/views/admin/sessions/show.blade.php`

**التحديثات**:
```javascript
ZoomMtg.init({
    leaveUrl: meetingConfig.leaveUrl,
    isSupportChat: true,      // تفعيل المحادثة
    isSupportQA: true,        // تفعيل الأسئلة والأجوبة
    isSupportCC: true,        // تفعيل الترجمة المباشرة
    screenShare: true,        // تفعيل مشاركة الشاشة
    disableRecord: false,     // السماح بالتسجيل
    // ...
});
```

### 3. ZoomWebhookController.php (جديد)
**الموقع**: `app/Http/Controllers/Admin/ZoomWebhookController.php`

**الوظيفة**: استقبال webhooks من Zoom وتحميل التسجيلات تلقائياً

**الأحداث المدعومة**:
- `recording.completed` - عند انتهاء التسجيل
- `meeting.ended` - عند انتهاء الاجتماع

### 4. Routes
**الموقع**: `routes/api.php`

**التحديث**:
```php
Route::post('/zoom/webhook', [ZoomWebhookController::class, 'handleWebhook']);
```

**URL الكامل**: `http://127.0.0.1:8000/api/zoom/webhook`

---

## 📝 خطوات إعداد Zoom Webhook

### 1. اذهب إلى Zoom App Marketplace
https://marketplace.zoom.us/user/build

### 2. افتح Server-to-Server OAuth App

### 3. اذهب إلى "Feature"
انقر على **Add Feature** → **Event Subscriptions**

### 4. أضف Webhook URL
```
http://127.0.0.1:8000/api/zoom/webhook
```

⚠️ **مهم**: إذا كنت تختبر محلياً، ستحتاج لاستخدام أداة مثل **ngrok** لعمل tunnel:
```bash
ngrok http 127.0.0.1:8000
```
ثم استخدم الـ URL الذي يعطيه ngrok (مثل `https://abc123.ngrok.io/api/zoom/webhook`)

### 5. أضف Event Types

اختر الأحداث التالية:
- ✅ `recording.completed` - عند انتهاء التسجيل
- ✅ `meeting.ended` - عند انتهاء الاجتماع

### 6. Webhook Secret Token

في `.env`، الـ webhook token موجود بالفعل:
```env
ZOOM_WEBHOOK_SECRET_TOKEN=5uRZEK30Q1q5DylNsNheEw
```

استخدم نفس القيمة في Zoom App settings.

### 7. احفظ التغييرات

انقر **Save** ثم **Activate** الـ feature.

---

## 🧪 اختبار الميزات

### اختبار التسجيل التلقائي:

1. افتح [http://127.0.0.1:8000/admin/sessions/create](http://127.0.0.1:8000/admin/sessions/create)
2. أنشئ session جديدة من نوع "Zoom Live"
3. افتح الـ session وانضم للاجتماع
4. سجّل فيديو قصير (30 ثانية كافية)
5. اخرج من الاجتماع
6. انتظر 5-10 دقائق لـ Zoom لمعالجة التسجيل
7. تحقق من logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
8. يجب أن يظهر التسجيل في ملفات الدرس تلقائياً!

### اختبار المحادثة (Chat):

1. انضم للاجتماع من [show.blade.php](http://127.0.0.1:8000/admin/sessions/{id})
2. ابحث عن أيقونة المحادثة (Chat) في واجهة Zoom
3. اكتب رسالة واختبر إرسالها
4. يمكنك المحادثة مع جميع المشاركين

---

## 📊 كيف يعمل التسجيل التلقائي؟

### سير العمل (Workflow):

```
1. المستخدم ينشئ Session جديدة
   ↓
2. يتم إنشاء Zoom meeting تلقائياً مع auto_recording=cloud
   ↓
3. المستخدم ينضم ويبدأ الاجتماع
   ↓
4. Zoom يسجل الاجتماع تلقائياً على الـ cloud
   ↓
5. عند انتهاء الاجتماع، Zoom يعالج التسجيل
   ↓
6. Zoom يرسل webhook إلى: /api/zoom/webhook
   ↓
7. ZoomWebhookController يستقبل الـ webhook
   ↓
8. يتم تحميل ملف MP4 من Zoom cloud
   ↓
9. يتم حفظ الملف في: storage/app/public/session-files/
   ↓
10. يتم إنشاء سجل SessionFile في قاعدة البيانات
   ↓
11. ✅ التسجيل يظهر في ملفات الدرس!
```

---

## 🔍 استكشاف الأخطاء (Troubleshooting)

### المشكلة: التسجيل لا يظهر

**الحلول**:

1. **تحقق من الـ logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **تحقق من Zoom webhook events**:
   - اذهب إلى Zoom App → Feature → Event Subscriptions
   - انظر في "Event Log" لمعرفة إذا تم إرسال webhooks

3. **تحقق من ngrok** (إذا كنت تختبر محلياً):
   ```bash
   ngrok http 127.0.0.1:8000
   ```
   افتح `http://127.0.0.1:4040` لرؤية الـ requests القادمة

4. **تحقق من الـ permissions**:
   ```bash
   php artisan storage:link
   chmod -R 775 storage/app/public/
   ```

### المشكلة: Chat لا يظهر

**الحلول**:

1. تأكد من أن الـ SDK version صحيح (3.8.10)
2. افتح console في المتصفح (F12) وابحث عن أخطاء
3. تأكد من `isSupportChat: true` في `ZoomMtg.init()`

---

## 📦 الملفات المعدّلة

1. ✅ `app/Services/ZoomService.php` - إضافة إعدادات التسجيل والمحادثة
2. ✅ `resources/views/admin/sessions/show.blade.php` - تفعيل Chat في Web SDK
3. ✅ `app/Http/Controllers/Admin/ZoomWebhookController.php` - معالج الـ webhooks (جديد)
4. ✅ `routes/api.php` - إضافة route للـ webhook

---

## 🎯 الخطوات التالية (اختياري)

### 1. إضافة إشعارات عند انتهاء التسجيل
في `ZoomWebhookController.php` → `handleRecordingCompleted()`:
```php
// إرسال إشعار للمعلم
$session->subject->teacher->notify(new RecordingReadyNotification($session));
```

### 2. عرض حالة التسجيل في الـ UI
أضف badge في `sessions/index.blade.php`:
```blade
@if($session->has_recording)
    <span class="badge badge-success">📹 تسجيل متوفر</span>
@endif
```

### 3. سماح للطلاب بتحميل التسجيلات
أضف permission check في SessionFile model.

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من `storage/logs/laravel.log`
2. تحقق من Zoom App Event Log
3. تحقق من ngrok logs (إذا كنت تستخدمه)

---

**تاريخ الإنشاء**: 2025-12-13
**الحالة**: ✅ جاهز للاختبار

**الميزات المفعّلة**:
- ✅ Auto Recording (Cloud)
- ✅ Chat (enabled for all)
- ✅ Webhook Integration
- ✅ Automatic Download & Storage
- ✅ Q&A
- ✅ Closed Captions
- ✅ Screen Sharing
