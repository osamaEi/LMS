# LMS Implementation - Next Steps Guide

## Status: ✅ Migrations Complete | ⏳ Models & Repositories Pending

### What's Been Completed:

1. ✅ Repository Pattern Structure (Base + Interfaces)
2. ✅ All Database Migrations:
   - `terms` table
   - `subjects` table
   - `enrollments` table
   - `sessions` table
   - `attendances` table
   - `evaluations` table
3. ✅ Model files created (need to be populated)
4. ✅ Comprehensive documentation

### File Locations:

**Migrations:**
- `database/migrations/2025_12_02_060706_create_terms_table.php`
- `database/migrations/2025_12_02_061151_create_subjects_table.php`
- `database/migrations/2025_12_02_061214_create_enrollments_table.php`
- `database/migrations/2025_12_02_061215_create_sessions_table.php`
- `database/migrations/2025_12_02_061215_create_attendances_table.php`
- `database/migrations/2025_12_02_061216_create_evaluations_table.php`

**Models (empty, need population):**
- `app/Models/Term.php`
- `app/Models/Subject.php`
- `app/Models/Enrollment.php`
- `app/Models/Session.php`
- `app/Models/Attendance.php`
- `app/Models/Evaluation.php`

**Repository Interfaces (complete):**
- `app/Repositories/Contracts/BaseRepositoryInterface.php`
- `app/Repositories/Contracts/TermRepositoryInterface.php`
- `app/Repositories/Contracts/SubjectRepositoryInterface.php`
- `app/Repositories/Contracts/EnrollmentRepositoryInterface.php`
- `app/Repositories/Contracts/SessionRepositoryInterface.php`
- `app/Repositories/Contracts/AttendanceRepositoryInterface.php`
- `app/Repositories/Contracts/EvaluationRepositoryInterface.php`

**Repository Implementations (need to be created):**
- `app/Repositories/Eloquent/BaseRepository.php` ✅
- `app/Repositories/Eloquent/TermRepository.php` ⏳
- `app/Repositories/Eloquent/SubjectRepository.php` ⏳
- `app/Repositories/Eloquent/EnrollmentRepository.php` ⏳
- `app/Repositories/Eloquent/SessionRepository.php` ⏳
- `app/Repositories/Eloquent/AttendanceRepository.php` ⏳
- `app/Repositories/Eloquent/EvaluationRepository.php` ⏳

---

## Quick Start: Run Migrations

```bash
php artisan migrate
```

This will create all 6 new tables in your database.

---

## Next Implementation Steps

### Step 1: Run Migrations
```bash
cd d:\mostaql\Lms
php artisan migrate
```

### Step 2: Request Model Implementation
Ask Claude Code:
> "Implement all 6 models (Term, Subject, Enrollment, Session, Attendance, Evaluation) with relationships and methods"

### Step 3: Request Repository Implementation
Ask Claude Code:
> "Implement all 6 repository classes in app/Repositories/Eloquent/"

### Step 4: Request Service Layer
Ask Claude Code:
> "Create service classes: TermService, SubjectService, EnrollmentService, SessionService, AttendanceService, EvaluationService, VideoUploadService, GradeCalculationService"

### Step 5: Request Controllers
Ask Claude Code:
> "Create controllers with repository dependency injection: TermController, SubjectController, EnrollmentController, SessionController, AttendanceController, EvaluationController"

### Step 6: Request Routes
Ask Claude Code:
> "Add all API routes for terms, subjects, enrollments, sessions, attendances, and evaluations"

### Step 7: Request Service Provider
Ask Claude Code:
> "Create RepositoryServiceProvider and register all repository bindings"

### Step 8: Request Seeders
Ask Claude Code:
> "Create seeders: TermSeeder, SubjectSeeder with sample data"

---

## Alternative: Batch Implementation

You can also ask Claude Code to implement everything in batches:

### Batch 1 - Core Academic Structure:
> "Implement Term and Subject models with their repositories, services, controllers, and routes"

### Batch 2 - Enrollment System:
> "Implement Enrollment model with repository, service, controller, and routes. Include auto-enrollment logic."

### Batch 3 - Session & Attendance:
> "Implement Session and Attendance models with repositories, services, controllers, VideoUploadService, and routes"

### Batch 4 - Evaluation & Grading:
> "Implement Evaluation model with repository, service, controller, GradeCalculationService, and routes"

---

## Database Schema Overview

### terms
- Stores academic terms/semesters
- Linked to programs
- Has registration periods
- Status: upcoming, active, completed, cancelled

### subjects
- Courses within a term
- Assigned to one teacher
- Has enrollment capacity
- Status: active, inactive, completed

### enrollments
- Student-subject relationship
- Tracks enrollment status
- Stores final grades
- Unique constraint on (student_id, subject_id)

### sessions
- Zoom meetings or recorded videos
- Belongs to subject
- Supports both live and recorded types
- Tracks session status

### attendances
- Student attendance per session
- Tracks video watch percentage
- Records join/leave times
- Unique constraint on (student_id, session_id)

### evaluations
- Assignments, quizzes, exams
- Linked to subject and student
- Has weight for final grade calculation
- Status: pending, submitted, graded, late

---

## Repository Pattern Flow

```
HTTP Request
    ↓
Controller (handles HTTP)
    ↓
Service (business logic)
    ↓
Repository (data access)
    ↓
Model (Eloquent ORM)
    ↓
Database
```

---

## Key Features to Implement

### Auto-Assignment (Term)
When student completes profile, automatically assign to:
- Current active term, OR
- Next upcoming term

### Enrollment Capacity (Subject)
Check if subject has available seats before allowing enrollment

### Attendance Tracking (Session)
- Zoom: Manual marking by teacher
- Video: Track watch percentage, mark attended when 100%

### Grade Calculation (Evaluation)
Apply weights to different evaluation types:
- Assignments: X%
- Midterm: Y%
- Final: Z%
- Attendance: W%

---

## Testing Checklist

After implementation, test these workflows:

1. ✅ Create program → Create terms → Create subjects
2. ✅ Student profile completion → Auto-assign to term
3. ✅ Student self-enrolls in subject
4. ✅ Teacher creates session (Zoom or video)
5. ✅ Teacher marks attendance OR student watches full video
6. ✅ Teacher creates evaluation
7. ✅ Teacher grades evaluation
8. ✅ System calculates final grade

---

## API Endpoints to Implement

### Terms
- GET /api/v1/terms
- GET /api/v1/terms/{id}
- POST /api/v1/terms (admin)
- PUT /api/v1/terms/{id} (admin)
- DELETE /api/v1/terms/{id} (admin)

### Subjects
- GET /api/v1/subjects
- GET /api/v1/subjects/{id}
- POST /api/v1/subjects (admin)
- PUT /api/v1/subjects/{id} (admin/teacher)
- DELETE /api/v1/subjects/{id} (admin)
- GET /api/v1/subjects/{id}/sessions
- GET /api/v1/subjects/{id}/students

### Enrollments
- GET /api/v1/enrollments (student: own enrollments)
- POST /api/v1/enrollments (student: enroll)
- DELETE /api/v1/enrollments/{id} (student: withdraw)
- GET /api/v1/subjects/{id}/check-enrollment

### Sessions
- GET /api/v1/sessions
- GET /api/v1/sessions/{id}
- POST /api/v1/sessions (teacher/admin)
- PUT /api/v1/sessions/{id} (teacher/admin)
- DELETE /api/v1/sessions/{id} (teacher/admin)
- POST /api/v1/sessions/{id}/upload-video (teacher)
- POST /api/v1/sessions/{id}/track-progress (student)

### Attendances
- GET /api/v1/sessions/{id}/attendance (teacher)
- POST /api/v1/sessions/{id}/attendance/mark (teacher)
- PUT /api/v1/attendances/{id} (teacher)
- GET /api/v1/students/me/attendance (student)

### Evaluations
- GET /api/v1/evaluations (filter by subject/student)
- GET /api/v1/evaluations/{id}
- POST /api/v1/evaluations (teacher)
- PUT /api/v1/evaluations/{id} (teacher: grade)
- POST /api/v1/evaluations/{id}/submit (student)
- GET /api/v1/students/me/grades

---

## Environment Variables Needed

Add to `.env`:

```env
# Video Storage
VIDEO_STORAGE_DISK=public
VIDEO_MAX_SIZE=500000  # 500MB in KB

# Grade Scale
GRADE_A_PLUS=95
GRADE_A=90
GRADE_B_PLUS=85
GRADE_B=80
GRADE_C_PLUS=75
GRADE_C=70
GRADE_D_PLUS=65
GRADE_D=60
```

---

## Documentation References

- [REPOSITORY_PATTERN_GUIDE.md](REPOSITORY_PATTERN_GUIDE.md) - Repository pattern usage
- [COMPLETE_PROFILE_README.md](COMPLETE_PROFILE_README.md) - Profile completion API
- [Implementation Plan](C:\Users\mo\.claude\plans\cached-marinating-newell.md) - Full system design

---

## Support

If you encounter issues:
1. Check migration status: `php artisan migrate:status`
2. Rollback if needed: `php artisan migrate:rollback`
3. Review error logs: `storage/logs/laravel.log`
4. Verify database connection in `.env`

---

**Ready to proceed? Run the migrations and ask Claude Code to implement the next batch!**
