# Teacher API Documentation

This document provides comprehensive documentation for all Teacher endpoints in the LMS system.

## Base URL
All teacher endpoints are prefixed with: `/api/v1/teacher`

## Authentication
All endpoints require authentication using Bearer token (Sanctum):
```
Authorization: Bearer {token}
```

---

## 1. Dashboard Endpoints

### 1.1 Get Teacher Dashboard
Get teacher's dashboard with statistics and overview.

**Endpoint:** `GET /api/v1/teacher/dashboard`

**Response:**
```json
{
    "success": true,
    "data": {
        "teacher": {
            "id": 1,
            "name": "أحمد محمد",
            "email": "teacher@lms.com"
        },
        "stats": {
            "total_subjects": 3,
            "total_students": 45,
            "upcoming_sessions": 5,
            "pending_evaluations": 8
        },
        "subjects": [...],
        "upcoming_sessions": [...],
        "pending_evaluations": [...]
    }
}
```

### 1.2 Get Teacher's Subjects
Get list of all subjects taught by the teacher.

**Endpoint:** `GET /api/v1/teacher/my-subjects`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "البرمجة بلغة Python",
            "code": "CS101",
            "students_count": 25,
            "units_count": 4,
            "sessions_count": 12
        }
    ]
}
```

---

## 2. Subject Management Endpoints

### 2.1 List Teacher's Subjects
Get paginated list of all subjects taught by the teacher.

**Endpoint:** `GET /api/v1/teacher/subjects`

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [...],
        "total": 3
    }
}
```

### 2.2 Get Subject Details
Get detailed information about a specific subject.

**Endpoint:** `GET /api/v1/teacher/subjects/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "البرمجة بلغة Python",
        "code": "CS101",
        "description": "...",
        "units": [
            {
                "id": 1,
                "name": "الوحدة الأولى",
                "sessions": [...]
            }
        ],
        "students_count": 25
    }
}
```

### 2.3 Get Subject Students
Get all students enrolled in a subject with attendance rates.

**Endpoint:** `GET /api/v1/teacher/subjects/{id}/students`

**Response:**
```json
{
    "success": true,
    "data": {
        "subject": {
            "id": 1,
            "name": "البرمجة بلغة Python",
            "code": "CS101"
        },
        "total_students": 25,
        "students": [
            {
                "enrollment_id": 1,
                "student": {
                    "id": 5,
                    "name": "محمد أحمد",
                    "email": "student@lms.com",
                    "national_id": "1234567890"
                },
                "enrolled_at": "2025-01-15",
                "status": "active",
                "progress_percentage": 45.5,
                "final_grade": null,
                "attended_sessions": 8,
                "total_sessions": 12,
                "attendance_rate": 66.67
            }
        ]
    }
}
```

### 2.4 Get Subject Statistics
Get comprehensive statistics for a subject.

**Endpoint:** `GET /api/v1/teacher/subjects/{id}/statistics`

**Response:**
```json
{
    "success": true,
    "data": {
        "subject": {
            "id": 1,
            "name": "البرمجة بلغة Python",
            "code": "CS101"
        },
        "statistics": {
            "students": {
                "total": 25,
                "by_status": {
                    "active": 22,
                    "completed": 2,
                    "dropped": 1,
                    "withdrawn": 0
                },
                "completed_course": 2
            },
            "sessions": {
                "total": 12,
                "completed": 8,
                "upcoming": 3,
                "in_progress": 1
            },
            "attendance": {
                "total_attendances": 180,
                "total_possible": 300,
                "overall_rate": 60.0
            },
            "progress": {
                "average_progress": 55.5,
                "students_completed": 2,
                "completion_rate": 8.0
            },
            "grades": {
                "average_grade": 78.5,
                "graded_students": 20,
                "passed_students": 18,
                "failed_students": 2,
                "pass_rate": 90.0
            }
        }
    }
}
```

---

## 3. Session Management Endpoints

### 3.1 List Sessions
Get all sessions for teacher's subjects with filtering.

**Endpoint:** `GET /api/v1/teacher/sessions`

**Query Parameters:**
- `subject_id` (optional): Filter by subject ID
- `status` (optional): Filter by status (scheduled, in_progress, completed, cancelled)
- `from_date` (optional): Filter sessions from this date
- `to_date` (optional): Filter sessions until this date
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "الجلسة الأولى: المقدمة",
                "session_number": 1,
                "type": "mixed",
                "duration": 90,
                "scheduled_at": "2025-01-20 10:00:00",
                "status": "scheduled",
                "subject": {...},
                "unit": {...}
            }
        ],
        "total": 12
    }
}
```

### 3.2 Get Session Details
Get detailed information about a specific session.

**Endpoint:** `GET /api/v1/teacher/sessions/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "الجلسة الأولى: المقدمة",
        "description": "...",
        "session_number": 1,
        "type": "mixed",
        "duration": 90,
        "scheduled_at": "2025-01-20 10:00:00",
        "status": "scheduled",
        "subject": {...},
        "unit": {...},
        "files": [...]
    }
}
```

### 3.3 Create Session
Create a new session.

**Endpoint:** `POST /api/v1/teacher/sessions`

**Request Body:**
```json
{
    "unit_id": 1,
    "title": "الجلسة الأولى",
    "description": "مقدمة عن البرمجة",
    "session_number": 1,
    "type": "video",
    "duration": 90,
    "scheduled_at": "2025-01-20 10:00:00",
    "status": "scheduled"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Session created successfully",
    "data": {...}
}
```

### 3.4 Update Session
Update session details.

**Endpoint:** `PUT /api/v1/teacher/sessions/{id}`

**Request Body:** (all fields optional)
```json
{
    "title": "الجلسة الأولى - محدثة",
    "status": "in_progress"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Session updated successfully",
    "data": {...}
}
```

### 3.5 Delete Session
Delete a session.

**Endpoint:** `DELETE /api/v1/teacher/sessions/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Session deleted successfully"
}
```

---

## 4. Session Files Management

### 4.1 Add File to Session
Add a video, PDF, or Zoom meeting to a session.

**Endpoint:** `POST /api/v1/teacher/sessions/{id}/files`

**Request Body (Video):**
```json
{
    "type": "video",
    "title": "فيديو الشرح - الجزء الأول",
    "description": "شرح مفصل عن...",
    "order": 1,
    "is_mandatory": true,
    "video_path": "videos/session_1/part1.mp4",
    "video_platform": "local",
    "video_duration": 45,
    "video_size": 104857600
}
```

**Request Body (PDF):**
```json
{
    "type": "pdf",
    "title": "ملف الشرح",
    "description": "ملاحظات الجلسة",
    "order": 2,
    "is_mandatory": false,
    "file_path": "pdfs/session_1/notes.pdf",
    "file_size": 5242880
}
```

**Request Body (Zoom):**
```json
{
    "type": "zoom",
    "title": "جلسة مباشرة",
    "description": "نقاش وأسئلة",
    "order": 3,
    "is_mandatory": true,
    "zoom_meeting_id": "123456789",
    "zoom_join_url": "https://zoom.us/j/123456789",
    "zoom_password": "abc123",
    "zoom_scheduled_at": "2025-01-20 10:00:00",
    "zoom_duration": 60
}
```

**Response:**
```json
{
    "success": true,
    "message": "File added to session successfully",
    "data": {...}
}
```

### 4.2 Update Session File
Update a file's details.

**Endpoint:** `PUT /api/v1/teacher/sessions/{sessionId}/files/{fileId}`

**Request Body:** (all fields optional)
```json
{
    "title": "عنوان محدث",
    "order": 2
}
```

**Response:**
```json
{
    "success": true,
    "message": "File updated successfully",
    "data": {...}
}
```

### 4.3 Delete Session File
Delete a file from a session.

**Endpoint:** `DELETE /api/v1/teacher/sessions/{sessionId}/files/{fileId}`

**Response:**
```json
{
    "success": true,
    "message": "File deleted successfully"
}
```

---

## 5. Attendance Management

### 5.1 Get Session Attendance
Get attendance records for a specific session with statistics.

**Endpoint:** `GET /api/v1/teacher/sessions/{sessionId}/attendance`

**Response:**
```json
{
    "success": true,
    "data": {
        "session": {
            "id": 1,
            "title": "الجلسة الأولى",
            "scheduled_at": "2025-01-20 10:00:00",
            "status": "completed"
        },
        "statistics": {
            "total_students": 25,
            "present": 20,
            "absent": 3,
            "late": 2,
            "not_marked": 0,
            "attendance_rate": 80.0
        },
        "attendance_list": [
            {
                "student": {
                    "id": 5,
                    "name": "محمد أحمد",
                    "email": "student@lms.com",
                    "national_id": "1234567890"
                },
                "attendance": {
                    "id": 1,
                    "status": "present",
                    "attended_at": "2025-01-20 10:05:00",
                    "video_watched_duration": 85,
                    "video_watched_percentage": 94.4,
                    "notes": null
                }
            }
        ]
    }
}
```

### 5.2 Mark Attendance
Mark attendance for a single student.

**Endpoint:** `POST /api/v1/teacher/sessions/{sessionId}/attendance`

**Request Body:**
```json
{
    "student_id": 5,
    "status": "present",
    "attended_at": "2025-01-20 10:05:00",
    "notes": "حضر في الموعد"
}
```

**Status Values:** `present`, `absent`, `late`, `excused`

**Response:**
```json
{
    "success": true,
    "message": "Attendance marked successfully",
    "data": {...}
}
```

### 5.3 Bulk Mark Attendance
Mark attendance for multiple students at once.

**Endpoint:** `POST /api/v1/teacher/sessions/{sessionId}/attendance/bulk`

**Request Body:**
```json
{
    "attendances": [
        {
            "student_id": 5,
            "status": "present",
            "notes": ""
        },
        {
            "student_id": 6,
            "status": "absent",
            "notes": "لم يحضر"
        },
        {
            "student_id": 7,
            "status": "late",
            "notes": "حضر متأخراً 15 دقيقة"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bulk attendance marking completed",
    "data": {
        "created": [...],
        "errors": [],
        "total_processed": 3,
        "total_created": 3,
        "total_errors": 0
    }
}
```

### 5.4 Update Attendance
Update an existing attendance record.

**Endpoint:** `PUT /api/v1/teacher/sessions/{sessionId}/attendance/{attendanceId}`

**Request Body:** (all fields optional)
```json
{
    "status": "present",
    "notes": "تم تعديل الحضور"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Attendance updated successfully",
    "data": {...}
}
```

### 5.5 Delete Attendance
Delete an attendance record.

**Endpoint:** `DELETE /api/v1/teacher/sessions/{sessionId}/attendance/{attendanceId}`

**Response:**
```json
{
    "success": true,
    "message": "Attendance record deleted successfully"
}
```

---

## 6. Evaluation Management

### 6.1 List Evaluations
Get all evaluations for teacher's subjects.

**Endpoint:** `GET /api/v1/teacher/evaluations`

**Query Parameters:**
- `subject_id` (optional): Filter by subject ID
- `type` (optional): Filter by type (assignment, quiz, exam, project)
- `status` (optional): Filter by status (draft, published, closed, graded)
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "type": "assignment",
                "title": "الواجب الأول",
                "description": "...",
                "total_marks": 100,
                "passing_marks": 50,
                "due_date": "2025-01-25",
                "status": "published",
                "subject": {...}
            }
        ],
        "total": 5
    }
}
```

### 6.2 Get Evaluation Details
Get detailed information about an evaluation with statistics.

**Endpoint:** `GET /api/v1/teacher/evaluations/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "evaluation": {
            "id": 1,
            "type": "assignment",
            "title": "الواجب الأول",
            "total_marks": 100,
            "submissions": [...]
        },
        "statistics": {
            "total_students": 25,
            "submitted": 20,
            "pending_submission": 5,
            "graded": 15,
            "pending_grading": 5,
            "average_grade": 78.5
        }
    }
}
```

### 6.3 Create Evaluation
Create a new evaluation (assignment, quiz, exam, or project).

**Endpoint:** `POST /api/v1/teacher/evaluations`

**Request Body:**
```json
{
    "subject_id": 1,
    "type": "assignment",
    "title": "الواجب الأول",
    "description": "حل التمارين من 1 إلى 10",
    "total_marks": 100,
    "passing_marks": 50,
    "due_date": "2025-01-25",
    "status": "published"
}
```

**Evaluation Types:** `assignment`, `quiz`, `exam`, `project`
**Status Values:** `draft`, `published`, `closed`, `graded`

**Response:**
```json
{
    "success": true,
    "message": "Evaluation created successfully",
    "data": {...}
}
```

### 6.4 Update Evaluation
Update evaluation details.

**Endpoint:** `PUT /api/v1/teacher/evaluations/{id}`

**Request Body:** (all fields optional)
```json
{
    "title": "الواجب الأول - محدث",
    "due_date": "2025-01-26",
    "status": "closed"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Evaluation updated successfully",
    "data": {...}
}
```

### 6.5 Delete Evaluation
Delete an evaluation.

**Endpoint:** `DELETE /api/v1/teacher/evaluations/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Evaluation deleted successfully"
}
```

### 6.6 Get Evaluation Submissions
Get all submissions for an evaluation.

**Endpoint:** `GET /api/v1/teacher/evaluations/{id}/submissions`

**Query Parameters:**
- `graded` (optional): Filter by grading status (true/false or 1/0)

**Response:**
```json
{
    "success": true,
    "data": {
        "evaluation": {
            "id": 1,
            "title": "الواجب الأول",
            "type": "assignment",
            "total_marks": 100
        },
        "submissions": [
            {
                "id": 1,
                "student": {
                    "id": 5,
                    "name": "محمد أحمد"
                },
                "submitted_at": "2025-01-24 14:30:00",
                "grade": 85,
                "feedback": "عمل ممتاز",
                "graded_at": "2025-01-25 10:00:00"
            }
        ]
    }
}
```

### 6.7 Grade Submission
Grade a single student's submission.

**Endpoint:** `POST /api/v1/teacher/evaluations/{evaluationId}/submissions/{submissionId}/grade`

**Request Body:**
```json
{
    "grade": 85,
    "feedback": "عمل ممتاز، استمر",
    "graded_at": "2025-01-25 10:00:00"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Submission graded successfully",
    "data": {...}
}
```

### 6.8 Bulk Grade Submissions
Grade multiple submissions at once.

**Endpoint:** `POST /api/v1/teacher/evaluations/{evaluationId}/submissions/bulk-grade`

**Request Body:**
```json
{
    "grades": [
        {
            "submission_id": 1,
            "grade": 85,
            "feedback": "عمل ممتاز"
        },
        {
            "submission_id": 2,
            "grade": 70,
            "feedback": "جيد"
        },
        {
            "submission_id": 3,
            "grade": 95,
            "feedback": "ممتاز جداً"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bulk grading completed",
    "data": {
        "updated": [...],
        "errors": [],
        "total_processed": 3,
        "total_updated": 3,
        "total_errors": 0
    }
}
```

---

## Error Responses

All endpoints return consistent error responses:

### 404 Not Found
```json
{
    "success": false,
    "message": "Subject not found or you do not have access to it"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "You do not have permission to create evaluations for this subject"
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "title": ["The title field is required."],
        "grade": ["The grade must not be greater than 100."]
    }
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "Attendance already marked for this student in this session"
}
```

---

## Notes

1. **Authorization**: All endpoints verify that the teacher has access to the requested resource (subject, session, evaluation, etc.)

2. **Pagination**: List endpoints support pagination with `per_page` parameter (default: 15)

3. **Filtering**: Most list endpoints support various filters via query parameters

4. **Bulk Operations**: Attendance and evaluation grading support bulk operations for efficiency

5. **Statistics**: Dashboard and subject statistics provide comprehensive analytics for teachers

6. **File Management**: Sessions support multiple files (videos, PDFs, Zoom meetings) with full CRUD operations

7. **Validation**: All create/update endpoints include comprehensive validation

8. **Relationships**: Responses include eagerly loaded relationships to minimize database queries
