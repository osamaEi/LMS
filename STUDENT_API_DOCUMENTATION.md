# Student Dashboard API Documentation

## نظرة عامة

هذا التوثيق يشرح API endpoints الخاصة بلوحة تحكم الطالب في نظام LMS.

## الهيكل الكامل للنظام

```
Program (البرنامج)
  └── Track (المسار) - يحتوي على 10 أرباع
      └── Term (الربع)
          └── Subject (المادة)
              └── Unit (الوحدة)
                  └── Session (الجلسة)
                      ├── Video (فيديو مسجل)
                      ├── Zoom (جلسة مباشرة)
                      ├── Files (ملفات)
                      ├── Attendance (الحضور)
                      └── Evaluation (التقييم)
```

---

## بيانات الاختبار (Test Data)

تم إنشاء البيانات التالية للاختبار:

### المدرسين (Teachers)
```
Email: teacher1@lms.com
Password: password123
الاسم: د. محمد أحمد
التخصص: هندسة البرمجيات

Email: teacher2@lms.com
Password: password123
الاسم: د. فاطمة علي
التخصص: أمن المعلومات

Email: teacher3@lms.com
Password: password123
الاسم: د. خالد سعيد
التخصص: قواعد البيانات
```

### الطلاب (Students)
```
Email: student1@lms.com
Password: password123
الاسم: أحمد محمود
المسار: الربع الأول

Email: student2@lms.com
Password: password123
الاسم: سارة عبدالله
المسار: الربع الأول

Email: student3@lms.com
Password: password123
الاسم: محمد حسن
المسار: الربع الأول
```

### المواد (Subjects) - الربع الأول
1. **مقدمة في البرمجة** (CS101-T1)
   - 3 وحدات
   - 9 جلسات (فيديوهات + Zoom)
   - 40 ساعة إجمالية

2. **قواعد البيانات** (CS102-T1)
   - 3 وحدات
   - 9 جلسات
   - 35 ساعة إجمالية

3. **تطوير الويب** (CS103-T1)
   - 3 وحدات
   - 9 جلسات
   - 45 ساعة إجمالية

---

## API Endpoints

### 1. تسجيل الدخول (Login)

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "student1@lms.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمود",
      "email": "student1@lms.com",
      "role": "student"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

**استخدم الـ Token في جميع الطلبات التالية:**
```
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

### 2. لوحة تحكم الطالب (Student Dashboard)

يعرض المواد الخاصة بالربع الحالي للطالب مع معلومات المدرس والتقدم.

```http
GET /api/v1/student/dashboard
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
  "success": true,
  "data": {
    "student": {
      "id": 1,
      "name": "أحمد محمود",
      "email": "student1@lms.com",
      "profile_photo": "https://ui-avatars.com/..."
    },
    "track": {
      "id": 1,
      "name": "مسار Diploma in IT الأساسي",
      "code": "TRACK-001-01"
    },
    "current_term": {
      "id": 1,
      "term_number": 1,
      "name": "الربع 1",
      "start_date": "2025-12-02",
      "end_date": "2026-03-01"
    },
    "subjects": [
      {
        "id": 1,
        "name": "مقدمة في البرمجة",
        "code": "CS101-T1",
        "description": "تعلم أساسيات البرمجة باستخدام لغة Python",
        "banner_photo": "https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800",
        "credits": 3,
        "total_hours": 40,
        "teacher": {
          "id": 1,
          "name": "د. محمد أحمد",
          "photo": "https://ui-avatars.com/...",
          "specialization": "هندسة البرمجيات"
        },
        "units_count": 3,
        "total_sessions": 9,
        "enrollment": {
          "status": "active",
          "enrolled_at": "2025-12-03T05:25:19.000000Z",
          "final_grade": null,
          "grade_letter": null
        },
        "progress": {
          "attended_sessions": 7,
          "total_sessions": 9,
          "completion_percentage": 77.78
        }
      }
      // ... more subjects
    ]
  }
}
```

**معلومات مهمة:**
- `units_count`: عدد الوحدات في المادة
- `total_hours`: إجمالي ساعات الفيديوهات
- `teacher`: معلومات المدرس (صورة، اسم، تخصص)
- `progress.completion_percentage`: نسبة إتمام الطالب للمادة

---

### 3. جميع المواد المسجل فيها الطالب

```http
GET /api/v1/student/my-subjects
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "subject": {
        "id": 1,
        "name": "مقدمة في البرمجة",
        "code": "CS101-T1",
        "banner_photo": "https://...",
        "total_hours": 40,
        "units_count": 3
      },
      "term": {
        "term_number": 1,
        "name": "الربع 1"
      },
      "teacher": {
        "name": "د. محمد أحمد",
        "photo": "https://..."
      },
      "enrollment": {
        "status": "active",
        "enrolled_at": "2025-12-03T05:25:19.000000Z",
        "final_grade": null,
        "grade_letter": null
      },
      "progress": {
        "completion_percentage": 77.78
      }
    }
    // ... more subjects
  ]
}
```

---

### 4. تفاصيل المادة الكاملة (مع الوحدات والجلسات)

يعرض كل شيء: الوحدات، الجلسات، الفيديوهات، Zoom، المدرس، التقدم.

```http
GET /api/v1/student/subjects/{subject_id}
Authorization: Bearer YOUR_TOKEN
```

**مثال:**
```http
GET /api/v1/student/subjects/1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "مقدمة في البرمجة",
      "code": "CS101-T1",
      "description": "تعلم أساسيات البرمجة باستخدام لغة Python",
      "banner_photo": "https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800",
      "credits": 3,
      "total_hours": 40,
      "status": "active"
    },
    "term": {
      "term_number": 1,
      "name": "الربع 1",
      "track": {
        "id": 1,
        "name": "مسار Diploma in IT الأساسي"
      }
    },
    "teacher": {
      "id": 1,
      "name": "د. محمد أحمد",
      "photo": "https://ui-avatars.com/...",
      "specialization": "هندسة البرمجيات",
      "bio": "خبرة 15 سنة في تطوير البرمجيات والتدريس الأكاديمي"
    },
    "enrollment": {
      "status": "active",
      "enrolled_at": "2025-12-03T05:25:19.000000Z",
      "final_grade": null,
      "grade_letter": null
    },
    "progress": {
      "attended_sessions": 7,
      "total_sessions": 9,
      "completion_percentage": 77.78
    },
    "units": [
      {
        "id": 1,
        "title": "الوحدة الأولى: المقدمة",
        "description": "مقدمة عن المادة والأهداف التعليمية",
        "unit_number": 1,
        "duration_hours": 8,
        "learning_objectives": "فهم المفاهيم الأساسية والأهداف",
        "sessions_count": 3,
        "completion_percentage": 100.0,
        "sessions": [
          {
            "id": 1,
            "title": "محاضرة 1: المقدمة",
            "description": "وصف الجلسة",
            "type": "recorded_video",
            "session_number": 1,
            "scheduled_at": "2025-12-04T05:25:19.000000Z",
            "duration_minutes": 45,
            "status": "completed",
            "is_mandatory": true,
            "video": {
              "url": "http://localhost:8000/storage/videos/unit_1/session_0.mp4",
              "duration": 45,
              "platform": "local"
            },
            "zoom": null,
            "my_attendance": {
              "attended": true,
              "watch_percentage": 95.00,
              "video_completed": true,
              "joined_at": "2025-11-30T05:25:19.000000Z",
              "duration_minutes": 43
            }
          },
          {
            "id": 2,
            "title": "محاضرة 2: الأساسيات",
            "description": "وصف الجلسة",
            "type": "recorded_video",
            "session_number": 2,
            "scheduled_at": "2025-12-05T05:25:19.000000Z",
            "duration_minutes": 60,
            "status": "completed",
            "is_mandatory": true,
            "video": {
              "url": "http://localhost:8000/storage/videos/unit_1/session_1.mp4",
              "duration": 60,
              "platform": "local"
            },
            "zoom": null,
            "my_attendance": {
              "attended": true,
              "watch_percentage": 87.00,
              "video_completed": true,
              "joined_at": "2025-12-01T05:25:19.000000Z",
              "duration_minutes": 52
            }
          },
          {
            "id": 3,
            "title": "جلسة نقاش مباشرة",
            "description": "وصف الجلسة",
            "type": "live_zoom",
            "session_number": 3,
            "scheduled_at": "2025-12-06T05:25:19.000000Z",
            "duration_minutes": 90,
            "status": "completed",
            "is_mandatory": true,
            "video": null,
            "zoom": {
              "join_url": "https://zoom.us/j/123456789",
              "meeting_id": "123456789",
              "password": null
            },
            "my_attendance": {
              "attended": true,
              "watch_percentage": 0.00,
              "video_completed": false,
              "joined_at": "2025-11-27T05:25:19.000000Z",
              "duration_minutes": 72
            }
          }
        ]
      },
      {
        "id": 2,
        "title": "الوحدة الثانية: المفاهيم المتقدمة",
        "description": "التعمق في المفاهيم المتقدمة",
        "unit_number": 2,
        "duration_hours": 12,
        "learning_objectives": "إتقان المفاهيم المتقدمة والتطبيق العملي",
        "sessions_count": 3,
        "completion_percentage": 66.67,
        "sessions": [
          // ... more sessions
        ]
      },
      {
        "id": 3,
        "title": "الوحدة الثالثة: التطبيقات العملية",
        "description": "تطبيق ما تم تعلمه في مشاريع عملية",
        "unit_number": 3,
        "duration_hours": 10,
        "learning_objectives": "القدرة على بناء مشاريع كاملة",
        "sessions_count": 3,
        "completion_percentage": 33.33,
        "sessions": [
          // ... more sessions
        ]
      }
    ]
  }
}
```

**معلومات مهمة:**

1. **معلومات المادة:**
   - `banner_photo`: صورة الغلاف للمادة
   - `total_hours`: إجمالي ساعات الفيديوهات
   - `credits`: عدد الوحدات الدراسية

2. **معلومات المدرس:**
   - `photo`: صورة المدرس
   - `specialization`: تخصص المدرس
   - `bio`: نبذة عن المدرس

3. **معلومات كل وحدة:**
   - `completion_percentage`: نسبة إتمام الطالب للوحدة
   - `sessions_count`: عدد الجلسات في الوحدة
   - `learning_objectives`: أهداف التعلم

4. **معلومات كل جلسة:**
   - `type`: نوع الجلسة (`recorded_video` أو `live_zoom`)
   - `video`: معلومات الفيديو (إذا كانت فيديو مسجل)
   - `zoom`: معلومات Zoom (إذا كانت جلسة مباشرة)
   - `my_attendance`: حضور الطالب:
     - `attended`: هل حضر؟
     - `watch_percentage`: نسبة المشاهدة (للفيديوهات)
     - `video_completed`: هل أكمل الفيديو كاملاً؟
     - `duration_minutes`: مدة الحضور بالدقائق

---

### 5. تفاصيل وحدة معينة

```http
GET /api/v1/student/units/{unit_id}
Authorization: Bearer YOUR_TOKEN
```

**مثال:**
```http
GET /api/v1/student/units/1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "unit": {
      "id": 1,
      "title": "الوحدة الأولى: المقدمة",
      "description": "مقدمة عن المادة والأهداف التعليمية",
      "unit_number": 1,
      "duration_hours": 8,
      "learning_objectives": "فهم المفاهيم الأساسية والأهداف"
    },
    "subject": {
      "id": 1,
      "name": "مقدمة في البرمجة",
      "code": "CS101-T1"
    },
    "completion_percentage": 100.0,
    "sessions": [
      {
        "id": 1,
        "title": "محاضرة 1: المقدمة",
        "description": "وصف الجلسة",
        "type": "recorded_video",
        "session_number": 1,
        "scheduled_at": "2025-12-04T05:25:19.000000Z",
        "duration_minutes": 45,
        "status": "completed",
        "video_url": "http://localhost:8000/storage/videos/unit_1/session_0.mp4",
        "zoom_join_url": null,
        "my_attendance": {
          "attended": true,
          "watch_percentage": 95.00,
          "video_completed": true
        }
      }
      // ... more sessions
    ]
  }
}
```

---

## ملخص الـ Endpoints

| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/api/v1/auth/login` | تسجيل الدخول والحصول على Token |
| GET | `/api/v1/student/dashboard` | لوحة تحكم الطالب - المواد الخاصة بالربع الحالي |
| GET | `/api/v1/student/my-subjects` | جميع المواد المسجل فيها الطالب |
| GET | `/api/v1/student/subjects/{id}` | تفاصيل مادة كاملة (وحدات، جلسات، مدرس، تقدم) |
| GET | `/api/v1/student/units/{id}` | تفاصيل وحدة معينة مع الجلسات |

---

## كيفية الاختبار

### 1. تسجيل الدخول
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "student1@lms.com",
    "password": "password123"
  }'
```

احفظ الـ Token من الـ response.

### 2. عرض لوحة التحكم
```bash
curl -X GET http://localhost:8000/api/v1/student/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. عرض تفاصيل مادة
```bash
curl -X GET http://localhost:8000/api/v1/student/subjects/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## البيانات المتوفرة

بعد تشغيل `CompleteSystemSeeder`:

- ✅ 3 مدرسين
- ✅ 3 طلاب (كلهم في الربع الأول)
- ✅ 6 مواد (3 في الربع الأول، 3 في الربع الثاني)
- ✅ 18 وحدة (3 وحدات لكل مادة)
- ✅ 54 جلسة (9 جلسات لكل مادة: 6 فيديوهات + 3 Zoom)
- ✅ سجلات حضور للطلاب (80% نسبة حضور عشوائية)
- ✅ تقييمات (واجب + اختبار منتصف الفصل لكل مادة)

---

## ملاحظات مهمة

1. **الحضور التلقائي للفيديوهات:**
   - يتم احتساب الطالب حاضراً عند مشاهدة 100% من الفيديو
   - `watch_percentage` يوضح نسبة المشاهدة
   - `video_completed` يوضح إذا أكمل الفيديو كاملاً

2. **الحضور اليدوي للـ Zoom:**
   - يتم تسجيل الحضور يدوياً من المدرس
   - `duration_minutes` توضح مدة الحضور الفعلي

3. **نسبة الإتمام:**
   - `completion_percentage` للوحدة = (الجلسات المحضورة / إجمالي الجلسات) × 100
   - `completion_percentage` للمادة = (الجلسات المحضورة / إجمالي الجلسات) × 100

4. **صور المدرسين والطلاب:**
   - جميع الصور تستخدم UI Avatars API
   - يمكن استبدالها بصور حقيقية لاحقاً

---

تم إنشاء النظام بنجاح ✅
