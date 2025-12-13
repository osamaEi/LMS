# 🔧 إصلاح مشكلة Zoom SDK Signature

## ❌ المشكلة

```
❌ Join error: Invalid signature., errorCode: 3712
```

## 🔍 السبب

أنت تستخدم **Server-to-Server OAuth App** للـ API، لكن **Zoom Web SDK يحتاج SDK App منفصل!**

هناك نوعان من الـ Apps في Zoom:

1. **Server-to-Server OAuth App** ← للـ API فقط (إنشاء، تعديل، حذف meetings)
2. **SDK App** ← للانضمام للـ meetings عبر Web SDK

## ✅ الحل

### الخطوة 1: إنشاء SDK App في Zoom Marketplace

1. اذهب إلى: https://marketplace.zoom.us/develop/create
2. اختر: **Meeting SDK**
3. اسم الـ App: `LMS Meeting SDK`
4. املأ المعلومات المطلوبة
5. في **App Credentials**، ستحصل على:
   - **SDK Key** (مثل Client ID لكن للـ SDK)
   - **SDK Secret** (مثل Client Secret لكن للـ SDK)

### الخطوة 2: تحديث .env

أضف السطور التالية للـ `.env`:

```env
# Zoom Server-to-Server OAuth (للـ API)
ZOOM_ACCOUNT_ID=4IiGyzxSS4qUwqT3u0VV3g
ZOOM_CLIENT_ID=c9hiCCeQQkKEVHVjGnvRKQ
ZOOM_CLIENT_SECRET=Hyx87I4EM94rA355ThJ68xeOO1Ce8GQY

# Zoom SDK App (للـ Web SDK)
ZOOM_SDK_KEY=YOUR_SDK_KEY_HERE
ZOOM_SDK_SECRET=YOUR_SDK_SECRET_HERE
```

**⚠️ مهم جداً**:
- `ZOOM_CLIENT_ID` و `ZOOM_CLIENT_SECRET` للـ **API فقط**
- `ZOOM_SDK_KEY` و `ZOOM_SDK_SECRET` للـ **Web SDK فقط**

### الخطوة 3: تحديث config/services.php

```php
'zoom' => [
    // Server-to-Server OAuth (API)
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),

    // SDK App (Web SDK)
    'sdk_key' => env('ZOOM_SDK_KEY'),
    'sdk_secret' => env('ZOOM_SDK_SECRET'),

    'webhook_secret_token' => env('ZOOM_WEBHOOK_SECRET_TOKEN'),
],
```

### الخطوة 4: تحديث ZoomService.php

```php
private string $sdkKey;
private string $sdkSecret;

public function __construct()
{
    // ... existing code ...
    $this->sdkKey = config('services.zoom.sdk_key', '');
    $this->sdkSecret = config('services.zoom.sdk_secret', '');
}

public function generateSignature(string $meetingNumber, int $role = 0): ?string
{
    // Use $this->sdkKey and $this->sdkSecret instead of clientId/clientSecret
    if (empty($this->sdkKey) || empty($this->sdkSecret)) {
        Log::error('Zoom SDK credentials not configured');
        return null;
    }

    $payload = [
        'sdkKey' => $this->sdkKey,  // ← استخدم SDK Key
        'mn' => (string) $meetingNumber,
        'role' => (int) $role,
        'iat' => time() - 30,
        'exp' => time() + 7200,
        'appKey' => $this->sdkKey,  // ← استخدم SDK Key
        'tokenExp' => time() + 7200,
    ];

    $signature = JWT::encode($payload, $this->sdkSecret, 'HS256');  // ← استخدم SDK Secret
    return $signature;
}
```

### الخطوة 5: تحديث show.blade.php

```javascript
ZoomMtg.join({
    signature: signatureData.signature,
    sdkKey: '{{ config("services.zoom.sdk_key") }}',  // ← SDK Key وليس Client ID
    meetingNumber: meetingConfig.meetingNumber,
    userName: meetingConfig.userName,
    userEmail: meetingConfig.userEmail,
    passWord: meetingConfig.password,
    // ...
});
```

---

## 🎯 الملخص

| الاستخدام | App Type | Credentials |
|-----------|----------|-------------|
| إنشاء/تعديل/حذف Meetings | Server-to-Server OAuth | CLIENT_ID + CLIENT_SECRET |
| الانضمام للـ Meetings (Web SDK) | SDK App | SDK_KEY + SDK_SECRET |

**لا يمكنك استخدام نفس الـ credentials للاثنين!**

---

## 📸 لقطات شاشة مطلوبة

بعد إنشاء SDK App، أرسل لقطة شاشة لـ:
1. SDK App Credentials (SDK Key + SDK Secret)
2. Features enabled في SDK App

---

## ⚡ الخطوات التالية

1. ✅ أنشئ SDK App في Zoom Marketplace
2. ✅ انسخ SDK Key و SDK Secret
3. ✅ أضفهم للـ `.env`
4. ✅ سأقوم بتحديث الكود تلقائياً بعد ذلك

---

**تاريخ الإنشاء**: 2025-12-13
**الحالة**: ⏳ بانتظار إنشاء SDK App
