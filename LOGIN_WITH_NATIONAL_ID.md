# تسجيل الدخول برقم الهوية الوطنية

## التغييرات المنفذة

تم تعديل نظام تسجيل الدخول ليدعم الدخول باستخدام **رقم الهوية الوطنية** أو **البريد الإلكتروني**.

---

## 🔧 التعديلات على Backend (Laravel)

### 1. LoginRequest (`app/Http/Requests/Auth/LoginRequest.php`)

**قبل:**
```php
'email' => ['required', 'string', 'email'],
'password' => ['required', 'string'],
```

**بعد:**
```php
'identifier' => ['required', 'string'], // يمكن أن يكون email أو national_id
'password' => ['required', 'string'],
```

### 2. LoginController (`app/Http/Controllers/Api/V1/Auth/LoginController.php`)

**قبل:**
```php
$user = User::where('email', $request->email)->first();
```

**بعد:**
```php
// تحديد نوع المعرف (email أو national_id)
$identifier = $request->identifier;
$field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'national_id';

// البحث عن المستخدم
$user = User::where($field, $identifier)->first();
```

**الآلية:**
1. النظام يتحقق تلقائياً إذا كان المدخل بريد إلكتروني (يحتوي على @)
2. إذا كان بريد إلكتروني → يبحث في حقل `email`
3. إذا لم يكن بريد إلكتروني → يبحث في حقل `national_id`

---

## 🎨 التعديلات على Frontend (Vue.js)

### 1. صفحة تسجيل الدخول (`lms_front/src/views/teacher/logIn.vue`)

**المميزات الجديدة:**

✅ **حقل موحد للإدخال:**
- يقبل رقم الهوية الوطنية أو البريد الإلكتروني
- Placeholder: "رقم الهوية أو البريد الإلكتروني..."

✅ **رسائل الأخطاء بالعربي:**
- "رقم الهوية أو كلمة المرور غير صحيحة"
- "تم إيقاف حسابك. يرجى التواصل مع الدعم"
- "تم رفض حسابك. يرجى التواصل مع الدعم"

✅ **رسائل النجاح:**
- "تم تسجيل الدخول بنجاح!"

✅ **Loading State:**
- زر معطل أثناء التسجيل
- Spinner يظهر أثناء الانتظار
- نص يتغير: "جاري تسجيل الدخول..."

✅ **التوجيه التلقائي حسب الدور:**
```javascript
if (user.role === "admin" || user.role === "super_admin") {
  this.$router.push("/admin/dashboard");
} else if (user.role === "teacher") {
  this.$router.push("/dashboard");
} else if (user.role === "student") {
  this.$router.push("/stDashBoard");
}
```

✅ **إخفاء/إظهار كلمة المرور:**
- أيقونة تفاعلية (عين)
- تبديل بين password و text

---

## 📝 API Request Format

### الطلب (Request)

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "identifier": "1234567890",  // رقم الهوية أو البريد
  "password": "password123"
}
```

### الاستجابة الناجحة (Success Response)

```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Ahmed Ali",
      "email": "ahmed@example.com",
      "national_id": "1234567890",
      "role": "admin",
      "status": "active"
    }
  }
}
```

### الاستجابة عند الخطأ (Error Response)

```json
{
  "success": false,
  "message": "Invalid credentials."
}
```

---

## 🚀 كيفية الاستخدام

### للمدير (Admin)

1. افتح صفحة تسجيل الدخول: `http://localhost:5173/logIn`
2. أدخل **رقم الهوية الوطنية** (مثال: `1234567890`)
3. أدخل **كلمة المرور**
4. اضغط "تسجيل الدخول"
5. سيتم توجيهك تلقائياً إلى `/admin/dashboard`

### لل متدرب أو ال مدرب 

يمكن استخدام:
- **رقم الهوية الوطنية**: `1234567890`
- **البريد الإلكتروني**: `user@example.com`

---

## ✅ اختبار النظام

### حالة 1: تسجيل دخول بالبريد الإلكتروني

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "admin@lms.com",
    "password": "password"
  }'
```

### حالة 2: تسجيل دخول برقم الهوية

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "1234567890",
    "password": "password"
  }'
```

---

## 🔐 الأمان

### ✅ التحقق من البيانات
- Laravel Validation على الـ Backend
- Required validation على الـ Frontend

### ✅ حماية الحساب
- التحقق من حالة الحساب (active, suspended, rejected)
- رسائل واضحة للمستخدم

### ✅ Token Authentication
- Laravel Sanctum للمصادقة
- Token يُحفظ في localStorage
- Auto redirect عند انتهاء الجلسة

---

## 📱 واجهة المستخدم

### قبل التعديل
- حقل واحد للبريد الإلكتروني فقط
- لا توجد رسائل أخطاء
- لا يوجد loading state

### بعد التعديل
✅ حقل موحد للبريد أو رقم الهوية
✅ رسائل أخطاء ونجاح بالعربي
✅ Loading spinner
✅ زر معطل أثناء الانتظار
✅ تحسينات UI/UX
✅ توجيه تلقائي حسب الدور

---

## 🐛 معالجة الأخطاء

### رسائل الأخطاء المترجمة:

| الحالة | الرسالة بالإنجليزي | الرسالة بالعربي |
|--------|-------------------|-----------------|
| بيانات خاطئة | Invalid credentials | رقم الهوية أو كلمة المرور غير صحيحة |
| حساب موقوف | Account suspended | تم إيقاف حسابك. يرجى التواصل مع الدعم |
| حساب مرفوض | Account rejected | تم رفض حسابك. يرجى التواصل مع الدعم |
| خطأ عام | Login failed | حدث خطأ أثناء تسجيل الدخول |

---

## 💡 نصائح للاستخدام

1. **للمدير:**
   - استخدم رقم الهوية الوطنية للدخول السريع
   - أو استخدم البريد الإلكتروني

2. **للطلاب وال مدرب ين:**
   - يمكن استخدام أي من الطريقتين
   - النظام يكتشف النوع تلقائياً

3. **الأمان:**
   - لا تشارك رقم الهوية أو كلمة المرور
   - استخدم كلمة مرور قوية

---

## 📊 ملخص التحسينات

| الميزة | الحالة |
|--------|--------|
| دعم رقم الهوية الوطنية | ✅ |
| دعم البريد الإلكتروني | ✅ |
| الكشف التلقائي عن النوع | ✅ |
| رسائل بالعربي | ✅ |
| Loading state | ✅ |
| معالجة الأخطاء | ✅ |
| التوجيه حسب الدور | ✅ |
| إخفاء/إظهار كلمة المرور | ✅ |
| UI/UX محسّن | ✅ |

---

## 🎉 النتيجة

نظام تسجيل دخول محسّن وسهل الاستخدام يدعم:
- ✅ رقم الهوية الوطنية (للمدير والجميع)
- ✅ البريد الإلكتروني (كبديل)
- ✅ كشف تلقائي ذكي
- ✅ رسائل واضحة بالعربي
- ✅ تجربة مستخدم ممتازة

**جاهز للاستخدام! 🚀**

---

**تاريخ التحديث:** 2025-12-06
**الإصدار:** 2.0.0
