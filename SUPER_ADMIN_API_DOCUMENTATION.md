# Super Admin API Documentation

Complete API documentation for LMS Super Admin panel to manage all system components.

## Table of Contents
- [Authentication](#authentication)
- [Users Management](#users-management)
- [Programs Management](#programs-management)
- [Tracks Management](#tracks-management)
- [Subjects Management](#subjects-management)
- [Units Management](#units-management)
- [Sessions Management](#sessions-management)
- [Enrollments Management](#enrollments-management)
- [Attendance Management](#attendance-management)
- [Evaluations Management](#evaluations-management)

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication

### Super Admin Credentials
```json
{
  "email": "admin@lms.com",
  "password": "password123"
}
```

### Login
**POST** `/auth/login`

**Request:**
```json
{
  "email": "admin@lms.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxx",
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "admin@lms.com",
      "role": "super_admin",
      "status": "active"
    }
  }
}
```

**Note:** Use the token in all subsequent requests:
```
Authorization: Bearer {token}
```

---

## Users Management

### Get All Users
**GET** `/admin/users`

**Query Parameters:**
- `role` - Filter by role (student, teacher, admin, super_admin)
- `status` - Filter by status (active, inactive, suspended)
- `program_id` - Filter by program
- `track_id` - Filter by track
- `search` - Search by name or email
- `per_page` - Results per page (default: 15)

**Examples:**
```
GET /admin/users?role=student
GET /admin/users?status=active&per_page=20
GET /admin/users?search=ahmed
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 6,
        "name": "أحمد محمود",
        "email": "student1@lms.com",
        "role": "student",
        "status": "active",
        "program": {
          "id": 1,
          "name": "Diploma in Information Technology"
        },
        "track": {
          "id": 1,
          "name": "مسار التقني الأساسي"
        },
        "current_term_number": 1,
        "created_at": "2025-12-03T05:25:18Z"
      }
    ],
    "per_page": 15,
    "total": 9
  }
}
```

### Get User Statistics
**GET** `/admin/users/stats`

**Response:**
```json
{
  "success": true,
  "data": {
    "total_users": 9,
    "students": 3,
    "teachers": 3,
    "admins": 1,
    "active_users": 9,
    "inactive_users": 0,
    "suspended_users": 0
  }
}
```

### Get User Details
**GET** `/admin/users/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 6,
    "name": "أحمد محمود",
    "email": "student1@lms.com",
    "role": "student",
    "program": {
      "id": 1,
      "name": "Diploma in Information Technology",
      "code": "DIT-2025"
    },
    "track": {
      "id": 1,
      "name": "مسار التقني الأساسي",
      "code": "TRACK-DIT-001"
    },
    "enrollments": [
      {
        "id": 1,
        "subject": {
          "id": 1,
          "name": "مقدمة في البرمجة",
          "code": "CS101-T1"
        },
        "status": "active",
        "enrolled_at": "2025-12-03T05:26:18Z"
      }
    ],
    "documents": []
  }
}
```

### Create User
**POST** `/admin/users`

**Request Body:**
```json
{
  "name": "New Student",
  "email": "newstudent@lms.com",
  "password": "password123",
  "role": "student",
  "phone": "0501234567",
  "national_id": "1234567890",
  "date_of_birth": "2000-01-01",
  "gender": "male",
  "program_id": 1,
  "track_id": 1,
  "current_term_number": 1,
  "status": "active"
}
```

**For Teachers:**
```json
{
  "name": "د. محمد السعيد",
  "email": "mohammed@lms.com",
  "password": "password123",
  "role": "teacher",
  "specialization": "علوم الحاسب",
  "bio": "خبرة 10 سنوات في التدريس الجامعي",
  "status": "active"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 10,
    "name": "New Student",
    "email": "newstudent@lms.com",
    "role": "student",
    "status": "active"
  }
}
```

### Update User
**PUT** `/admin/users/{id}`

**Request Body:**
```json
{
  "name": "Updated Name",
  "status": "active",
  "current_term_number": 2
}
```

### Delete User
**DELETE** `/admin/users/{id}`

**Response:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

---

## Programs Management

### Get All Programs
**GET** `/admin/programs`

### Get Program Details
**GET** `/admin/programs/{id}`

### Create Program
**POST** `/admin/programs`

**Request:**
```json
{
  "name": "Bachelor of Computer Science",
  "code": "BCS-2025",
  "description": "Complete computer science program",
  "duration_months": 48,
  "type": "diploma",
  "status": "active"
}
```

### Update Program
**PUT** `/admin/programs/{id}`

### Delete Program
**DELETE** `/admin/programs/{id}`

---

## Tracks Management

### Get All Tracks
**GET** `/tracks`

### Get Track Details
**GET** `/tracks/{id}?with_terms=true`

### Create Track
**POST** `/tracks`

**Request:**
```json
{
  "program_id": 1,
  "name": "مسار الذكاء الاصطناعي",
  "code": "AI-TRACK-01",
  "description": "مسار متخصص في الذكاء الاصطناعي",
  "total_terms": 10,
  "duration_months": 30,
  "status": "active",
  "auto_create_terms": true,
  "term_duration_months": 3
}
```

### Update Track
**PUT** `/tracks/{id}`

### Assign Student to Track
**POST** `/tracks/{id}/assign-student`

**Request:**
```json
{
  "student_id": 6,
  "term_number": 1
}
```

### Promote Student to Next Term
**POST** `/tracks/promote-student`

**Request:**
```json
{
  "student_id": 6
}
```

---

## Subjects Management

### Get All Subjects
**GET** `/admin/subjects`

### Get Subject Details
**GET** `/admin/subjects/{id}`

### Create Subject
**POST** `/admin/subjects`

**Request:**
```json
{
  "term_id": 1,
  "teacher_id": 3,
  "name": "البرمجة المتقدمة",
  "code": "CS201",
  "description": "تعلم البرمجة المتقدمة وأنماط التصميم",
  "banner_photo": "https://example.com/banner.jpg",
  "credits": 4,
  "total_hours": 48,
  "max_students": 30,
  "status": "active"
}
```

### Update Subject
**PUT** `/admin/subjects/{id}`

### Delete Subject
**DELETE** `/admin/subjects/{id}`

---

## Units Management

### Get All Units
**GET** `/admin/units`

### Get Unit Details
**GET** `/admin/units/{id}`

### Create Unit
**POST** `/admin/units`

**Request:**
```json
{
  "subject_id": 1,
  "title": "الوحدة الرابعة: المشاريع",
  "description": "بناء مشاريع كاملة",
  "unit_number": 4,
  "duration_hours": 15,
  "learning_objectives": "تطبيق جميع المفاهيم في مشروع عملي",
  "status": "published",
  "order": 4
}
```

### Update Unit
**PUT** `/admin/units/{id}`

### Delete Unit
**DELETE** `/admin/units/{id}`

---

## Sessions Management

### Get All Sessions
**GET** `/admin/sessions`

### Get Session Details
**GET** `/admin/sessions/{id}`

### Create Session
**POST** `/admin/sessions`

**Request:**
```json
{
  "subject_id": 1,
  "unit_id": 1,
  "title": "الجلسة الثالثة: التطبيق",
  "description": "تطبيق عملي على المفاهيم",
  "session_number": 3,
  "type": "mixed",
  "scheduled_at": "2025-12-10T18:00:00Z",
  "duration_minutes": 120,
  "status": "scheduled",
  "is_mandatory": true
}
```

**Session Types:**
- `recorded_video` - Video session
- `live_zoom` - Live Zoom session
- `mixed` - Multiple files (videos, PDFs, Zoom)

**Session Status:**
- `scheduled` - Not started yet
- `live` - Currently ongoing
- `completed` - Finished
- `cancelled` - Cancelled

### Update Session
**PUT** `/admin/sessions/{id}`

### Delete Session
**DELETE** `/admin/sessions/{id}`

---

## Enrollments Management

### Get All Enrollments
**GET** `/admin/enrollments`

### Get Enrollment Details
**GET** `/admin/enrollments/{id}`

### Create Enrollment
**POST** `/admin/enrollments`

**Request:**
```json
{
  "student_id": 6,
  "subject_id": 1,
  "status": "active"
}
```

### Update Enrollment
**PUT** `/admin/enrollments/{id}`

**Request:**
```json
{
  "status": "completed",
  "final_grade": 95,
  "grade_letter": "A"
}
```

**Enrollment Status:**
- `active` - Currently enrolled
- `completed` - Successfully completed
- `withdrawn` - Student withdrew
- `failed` - Failed the course

### Delete Enrollment
**DELETE** `/admin/enrollments/{id}`

---

## Attendance Management

### Get All Attendance
**GET** `/admin/attendances`

### Get Attendance Details
**GET** `/admin/attendances/{id}`

### Create Attendance
**POST** `/admin/attendances`

**Request:**
```json
{
  "student_id": 6,
  "session_id": 1,
  "attended": true,
  "watch_percentage": 100,
  "video_completed": true,
  "joined_at": "2025-12-04T18:00:00Z",
  "duration_minutes": 45
}
```

### Update Attendance
**PUT** `/admin/attendances/{id}`

### Delete Attendance
**DELETE** `/admin/attendances/{id}`

---

## Evaluations Management

### Get All Evaluations
**GET** `/admin/evaluations`

### Get Evaluation Details
**GET** `/admin/evaluations/{id}`

### Create Evaluation
**POST** `/admin/evaluations`

**Request:**
```json
{
  "subject_id": 1,
  "student_id": 6,
  "type": "final_exam",
  "title": "الاختبار النهائي",
  "total_score": 100,
  "earned_score": 88,
  "percentage": 88,
  "weight": 50,
  "status": "graded"
}
```

**Evaluation Types:**
- `assignment` - Assignment
- `quiz` - Quiz
- `midterm_exam` - Midterm exam
- `final_exam` - Final exam
- `project` - Project
- `participation` - Class participation

**Evaluation Status:**
- `pending` - Not graded yet
- `graded` - Graded
- `submitted` - Submitted but not graded

### Update Evaluation
**PUT** `/admin/evaluations/{id}`

### Delete Evaluation
**DELETE** `/admin/evaluations/{id}`

---

## Error Responses

All endpoints may return the following error responses:

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "You cannot delete your own account"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## Testing with Postman

1. Import the collection: `LMS_SuperAdmin_API.postman_collection.json`
2. Set base_url variable: `http://localhost:8000`
3. Login as Super Admin (email: admin@lms.com, password: password123)
4. Token will be automatically saved to `admin_token` variable
5. All subsequent requests will use this token automatically

---

## Notes

- All dates are in ISO 8601 format (YYYY-MM-DDTHH:MM:SSZ)
- All timestamps are in UTC
- Pagination is available on list endpoints
- Soft deletes are enabled on all models
- File uploads are handled separately via multipart/form-data

---

## System Architecture

### Models Hierarchy
```
Program
  └── Track (10 Terms)
      └── Term
          └── Subject
              └── Unit
                  └── Session
                      └── SessionFile (Videos, PDFs, Zoom)
```

### User Roles
- `super_admin` - Full system access
- `admin` - Administrative access
- `teacher` - Can manage their subjects
- `student` - Can view enrolled subjects

### Session Files Structure
Each session can have:
- Multiple videos (local or YouTube/Vimeo)
- Multiple PDF files
- Multiple Zoom meetings

Example:
```json
{
  "session_id": 1,
  "files": [
    {
      "type": "video",
      "title": "الفيديو التعليمي - الجزء الأول",
      "video": {
        "url": "http://localhost:8000/storage/videos/session_1/part1.mp4",
        "duration": 45,
        "platform": "local",
        "size": "100 MB"
      }
    },
    {
      "type": "pdf",
      "title": "ملف الشرح",
      "pdf": {
        "url": "http://localhost:8000/storage/pdfs/session_1/notes.pdf",
        "size": "5 MB"
      }
    },
    {
      "type": "zoom",
      "title": "جلسة مباشرة",
      "zoom": {
        "join_url": "https://zoom.us/j/123456789",
        "meeting_id": "123456789",
        "password": "pass123",
        "scheduled_at": "2025-12-04T18:00:00Z"
      }
    }
  ]
}
```

---

## Support

For issues or questions:
- Check the Postman collection examples
- Review this documentation
- Contact the development team
