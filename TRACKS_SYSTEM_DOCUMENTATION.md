# نظام المسارات (Tracks System) - التوثيق الكامل

## نظرة عامة

نظام المسارات هو نظام تعليمي متكامل حيث:
- كل **برنامج** (Program) يحتوي على **مسارات** متعددة
- كل **مسار** (Track) يحتوي على **10 أرباع** (Terms)
- كل **ربع** (Term) يحتوي على **مواد دراسية** (Subjects/Courses)
- كل **مادة** تحتوي على **جلسات** (Sessions: Zoom أو فيديوهات مسجلة)
- كل جلسة لها **حضور** (Attendance) و **تقييمات** (Evaluations)

---

## هيكل قاعدة البيانات

### 1. جدول Tracks (المسارات)
```sql
- id
- program_id (FK -> programs)
- name (اسم المسار)
- code (كود فريد للمسار)
- description (وصف المسار)
- total_terms (عدد الأرباع - افتراضي: 10)
- duration_months (مدة المسار بالأشهر)
- status (active, inactive, archived)
- created_at, updated_at, deleted_at
```

### 2. جدول Terms (الأرباع)
```sql
- id
- program_id (FK -> programs)
- track_id (FK -> tracks) ← جديد
- term_number (رقم الربع: 1-10)
- name (اسم الربع)
- start_date (تاريخ البداية)
- end_date (تاريخ النهاية)
- registration_start_date (بداية التسجيل)
- registration_end_date (نهاية التسجيل)
- status (upcoming, active, completed, cancelled)
- created_at, updated_at, deleted_at
```

### 3. جدول Users (تحديث الطلاب)
```sql
الحقول الجديدة:
- track_id (FK -> tracks) ← المسار المسجل فيه الطالب
- current_term_number (رقم الربع الحالي: 1-10) ← في أي ربع الطالب حالياً
```

---

## العلاقات (Relationships)

### Track Model
```php
- belongsTo: Program
- hasMany: Terms (10 أرباع)
- hasMany: Students (Users where track_id)
- hasManyThrough: Enrollments
```

### Term Model
```php
- belongsTo: Program
- belongsTo: Track ← جديد
- hasMany: Subjects
- hasManyThrough: Enrollments
```

### User Model (Student)
```php
- belongsTo: Program
- belongsTo: Track ← جديد
- hasMany: Enrollments
```

---

## API Endpoints

### Track Management

#### 1. عرض جميع المسارات
```http
GET /api/v1/tracks
Query Parameters:
- program_id (optional): فلترة حسب البرنامج
- status (optional): فلترة حسب الحالة (active/inactive)

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "program_id": 1,
      "name": "مسار Diploma in IT الأساسي",
      "code": "TRACK-001-01",
      "total_terms": 10,
      "status": "active",
      "program": {...}
    }
  ]
}
```

#### 2. عرض مسار واحد
```http
GET /api/v1/tracks/{id}
Query Parameters:
- with_terms (boolean): تحميل الأرباع
- with_students (boolean): تحميل الطلاب

Response:
{
  "success": true,
  "data": {
    "id": 1,
    "name": "مسار Diploma in IT الأساسي",
    "terms": [...], // إذا with_terms=true
    "students": [...] // إذا with_students=true
  }
}
```

#### 3. إنشاء مسار جديد
```http
POST /api/v1/tracks
Content-Type: application/json

Body:
{
  "program_id": 1,
  "name": "المسار الأساسي",
  "code": "TRACK-001-01",
  "description": "وصف المسار",
  "total_terms": 10,
  "duration_months": 30,
  "status": "active",
  "auto_create_terms": true, // إنشاء 10 أرباع تلقائياً
  "term_duration_months": 3 // مدة كل ربع
}

Response:
{
  "success": true,
  "message": "Track created successfully",
  "data": {...}
}
```

#### 4. تحديث مسار
```http
PUT /api/v1/tracks/{id}
Content-Type: application/json

Body:
{
  "name": "اسم جديد",
  "status": "inactive"
}
```

#### 5. حذف مسار
```http
DELETE /api/v1/tracks/{id}
```

#### 6. عرض أرباع المسار
```http
GET /api/v1/tracks/{id}/terms

Response:
{
  "success": true,
  "data": {
    "track": {...},
    "terms": [
      {
        "id": 1,
        "term_number": 1,
        "name": "الربع 1",
        "start_date": "2025-12-02",
        "end_date": "2026-03-01",
        "status": "active"
      },
      // ... 9 أرباع أخرى
    ]
  }
}
```

#### 7. تعيين طالب إلى مسار
```http
POST /api/v1/tracks/{id}/assign-student
Content-Type: application/json

Body:
{
  "student_id": 1,
  "term_number": 1 // اختياري، افتراضي: 1
}

Response:
{
  "success": true,
  "message": "Student assigned to track successfully"
}
```

#### 8. ترقية طالب للربع التالي
```http
POST /api/v1/tracks/promote-student
Content-Type: application/json

Body:
{
  "student_id": 1
}

Response:
{
  "success": true,
  "message": "Student promoted to next term successfully"
}
```

---

## الوظائف الرئيسية

### TrackService Methods

#### 1. إنشاء مسار مع أرباعه تلقائياً
```php
$trackService->createTrack([
    'program_id' => 1,
    'name' => 'المسار الأساسي',
    'code' => 'TRACK-001',
    'auto_create_terms' => true,
    'term_duration_months' => 3
]);
```
**الوظيفة:** ينشئ مسار + 10 أرباع تلقائياً

#### 2. تعيين طالب لمسار
```php
$trackService->assignStudentToTrack($studentId, $trackId, $termNumber = 1);
```
**الوظيفة:** يضيف الطالب للمسار ويحدد الربع الحالي

#### 3. ترقية طالب للربع التالي
```php
$trackService->promoteStudentToNextTerm($studentId);
```
**الوظيفة:** ينقل الطالب من الربع الحالي للربع التالي (مثلاً من 1 إلى 2)

#### 4. الحصول على الربع الحالي النشط
```php
$track->getCurrentActiveTerm();
```
**الوظيفة:** يعيد الربع النشط حالياً في المسار

#### 5. الحصول على الربع التالي
```php
$track->getNextTerm($currentTermNumber);
```
**الوظيفة:** يعيد الربع التالي بعد الربع المحدد

---

## سيناريوهات الاستخدام

### السيناريو 1: إنشاء مسار جديد مع 10 أرباع
```php
// 1. إنشاء المسار
POST /api/v1/tracks
{
    "program_id": 1,
    "name": "مسار الأمن السيبراني",
    "code": "CYBER-TRACK-01",
    "total_terms": 10,
    "auto_create_terms": true,
    "term_duration_months": 3
}

// النتيجة: مسار + 10 أرباع تم إنشاؤها تلقائياً
```

### السيناريو 2: تسجيل طالب في مسار
```php
// 1. تعيين الطالب للمسار (يبدأ من الربع 1)
POST /api/v1/tracks/1/assign-student
{
    "student_id": 5,
    "term_number": 1
}

// 2. الطالب الآن في:
// - track_id = 1
// - current_term_number = 1
```

### السيناريو 3: ترقية الطالب للربع التالي
```php
// بعد انتهاء الربع الأول
POST /api/v1/tracks/promote-student
{
    "student_id": 5
}

// الطالب الآن في:
// - track_id = 1 (نفس المسار)
// - current_term_number = 2 (الربع التالي)
```

### السيناريو 4: عرض مسار الطالب الكامل
```php
GET /api/v1/tracks/1?with_terms=true

Response:
{
  "track": {
    "id": 1,
    "name": "مسار الأمن السيبراني",
    "total_terms": 10,
    "terms": [
      {"term_number": 1, "status": "completed"},
      {"term_number": 2, "status": "active"},
      {"term_number": 3, "status": "upcoming"},
      // ... 7 أرباع أخرى
    ]
  }
}
```

---

## الـ Helper Methods المتاحة

### Track Model
```php
$track->isActive()                    // هل المسار نشط؟
$track->hasAllTerms()                 // هل المسار يحتوي على 10 أرباع؟
$track->getTotalStudents()            // عدد الطلاب في المسار
$track->getCurrentActiveTerm()        // الربع النشط حالياً
$track->getTermByNumber(5)            // الحصول على الربع رقم 5
$track->getNextTerm(2)                // الحصول على الربع التالي بعد 2
$track->getCompletionPercentage()     // نسبة إتمام المسار
```

### Term Model
```php
$term->isActive()                     // هل الربع نشط؟
$term->isRegistrationOpen()           // هل التسجيل مفتوح؟
$term->hasStarted()                   // هل الربع بدأ؟
$term->hasEnded()                     // هل الربع انتهى؟
```

---

## قواعد العمل (Business Rules)

1. **المسار يحتوي على 10 أرباع فقط**
   - لا يمكن ترقية طالب بعد الربع 10

2. **الطالب يكون في ربع واحد فقط في وقت واحد**
   - `current_term_number` يحدد موقع الطالب

3. **التسجيل في المواد يكون من خلال الربع الحالي**
   - الطالب يسجل فقط في مواد الربع الذي هو فيه

4. **الأرباع لها حالات محددة**
   - `upcoming`: لم يبدأ بعد
   - `active`: جاري الآن
   - `completed`: انتهى
   - `cancelled`: ملغي

---

## ملفات النظام

### Database
- `migrations/2025_12_02_145636_create_tracks_table.php`
- `migrations/2025_12_02_145700_add_track_id_to_terms_table.php`
- `migrations/2025_12_02_145715_add_track_id_to_users_table.php`

### Models
- `app/Models/Track.php`
- `app/Models/Term.php` (محدث)
- `app/Models/User.php` (محدث)

### Repositories
- `app/Repositories/Contracts/TrackRepositoryInterface.php`
- `app/Repositories/Eloquent/TrackRepository.php`

### Services
- `app/Services/TrackService.php`

### Controllers
- `app/Http/Controllers/Api/V1/TrackController.php`

### Seeders
- `database/seeders/TrackSeeder.php`

### Routes
- `routes/api.php` (محدث بـ Track routes)

### Service Provider
- `app/Providers/RepositoryServiceProvider.php`

---

## اختبار النظام

### 1. عرض جميع المسارات
```bash
curl -X GET http://localhost:8000/api/v1/tracks \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. إنشاء مسار جديد
```bash
curl -X POST http://localhost:8000/api/v1/tracks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "program_id": 1,
    "name": "مسار تجريبي",
    "code": "TEST-TRACK-01",
    "total_terms": 10,
    "auto_create_terms": true
  }'
```

### 3. تعيين طالب لمسار
```bash
curl -X POST http://localhost:8000/api/v1/tracks/1/assign-student \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "term_number": 1
  }'
```

---

## الخطوات التالية

1. ✅ نظام المسارات (Tracks) - مكتمل
2. ⏳ إنشاء Controllers للمواد (Subjects)
3. ⏳ إنشاء Controllers للجلسات (Sessions)
4. ⏳ إنشاء Controllers للحضور (Attendance)
5. ⏳ إنشاء Controllers للتقييمات (Evaluations)
6. ⏳ إنشاء نظام التسجيل في المواد (Enrollment)

---

## ملاحظات مهمة

- جميع الـ Routes محمية بـ `auth:sanctum`
- الـ Repository Pattern مستخدم في جميع العمليات
- الـ Soft Deletes مفعل على Tracks و Terms
- الـ Service Provider مسجل في `bootstrap/providers.php`
- البيانات التجريبية متوفرة من خلال TrackSeeder

---

تم إنشاء النظام بنجاح ✅
