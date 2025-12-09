# 🔧 حل المشاكل (Troubleshooting)

## ❌ مشكلة: خطأ 500 عند تسجيل الدخول

### السبب:
```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

**المشكلة:** MySQL غير مشغل!

---

## ✅ الحل: تشغيل MySQL

### الطريقة 1: XAMPP

1. افتح XAMPP Control Panel
2. ابحث عن MySQL في القائمة
3. اضغط زر **Start** بجانب MySQL
4. انتظر حتى يصبح اللون أخضر

### الطريقة 2: WAMP

1. افتح WAMP
2. انقر على أيقونة WAMP في System Tray
3. MySQL → Service → Start/Resume Service

### الطريقة 3: Windows Services

1. اضغط `Win + R`
2. اكتب `services.msc`
3. ابحث عن خدمة MySQL (مثل MySQL80)
4. انقر بزر الماوس الأيمن → Start

---

## 🧪 التحقق من تشغيل MySQL

في PowerShell أو CMD:

```bash
# اختبار الاتصال
mysql -u root -p

# إذا نجح الاتصال، اكتب:
SHOW DATABASES;
```

---

## 🚀 بعد تشغيل MySQL

### 1. إنشاء قاعدة البيانات (إذا لم تكن موجودة)

```bash
cd e:\mostaql\Lms

# إنشاء قاعدة البيانات
php artisan db:create

# أو يدوياً في MySQL:
mysql -u root -p
CREATE DATABASE LMS2;
EXIT;
```

### 2. تشغيل Migrations

```bash
php artisan migrate
```

### 3. إنشاء المدير

```bash
php artisan db:seed --class=SuperAdminSeeder
```

الناتج المتوقع:
```
Super Admin created successfully!
Email: admin@lms.com
National ID: 1234567890
Password: password123
```

### 4. تشغيل Laravel Server

```bash
php artisan serve
```

### 5. اختبار تسجيل الدخول

افتح: `http://localhost:5173/logIn`

أدخل:
- رقم الهوية: `1234567890`
- كلمة المرور: `password123`

---

## 🔍 اختبار الاتصال بقاعدة البيانات

```bash
# من Laravel
php artisan tinker
>>> DB::connection()->getPdo();

# يجب أن يظهر:
# PDO object بدون أخطاء
```

---

## 📊 التحقق من وجود المدير

```bash
php artisan tinker
>>> User::where('national_id', '1234567890')->first();
```

يجب أن يظهر:
```php
App\Models\User {
  id: 1,
  name: "Super Admin",
  email: "admin@lms.com",
  national_id: "1234567890",
  role: "super_admin",
  status: "active",
}
```

---

## ⚠️ مشاكل شائعة أخرى

### 1. "Access denied for user 'root'@'localhost'"

**الحل:** تحديث كلمة مرور MySQL في `.env`

```env
DB_PASSWORD=your_mysql_password
```

### 2. "Database 'LMS2' doesn't exist"

**الحل:**
```bash
mysql -u root -p
CREATE DATABASE LMS2;
```

### 3. "CORS Error" من Frontend

**الحل:** تحديث `config/cors.php`:

```php
'allowed_origins' => ['http://localhost:5173'],
```

### 4. "Route not found"

**الحل:**
```bash
php artisan route:clear
php artisan route:cache
```

---

## 🎯 الخطوات الكاملة للبدء من الصفر

```bash
# 1. تشغيل MySQL (من XAMPP/WAMP)

# 2. إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE LMS2;
EXIT;

# 3. تشغيل Migrations
cd e:\mostaql\Lms
php artisan migrate

# 4. إنشاء البيانات الأساسية
php artisan db:seed --class=SuperAdminSeeder

# 5. تشغيل Laravel
php artisan serve

# 6. تشغيل Vue (نافذة أخرى)
cd lms_front
npm run dev

# 7. فتح المتصفح
http://localhost:5173/logIn
```

---

## 📞 إذا استمرت المشكلة

1. تحقق من logs:
   ```bash
   tail -50 storage/logs/laravel.log
   ```

2. تحقق من `.env`:
   ```bash
   cat .env | grep DB_
   ```

3. اختبر الاتصال:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

---

## ✅ علامات النجاح

- ✅ MySQL مشغل (XAMPP/WAMP باللون الأخضر)
- ✅ قاعدة البيانات `LMS2` موجودة
- ✅ Laravel server يعمل على `http://localhost:8000`
- ✅ Vue server يعمل على `http://localhost:5173`
- ✅ تسجيل الدخول ينجح بدون أخطاء

---

**آخر تحديث:** 2025-12-06
