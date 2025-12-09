# Dashboard Quick Start Guide

## Getting Started

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Create Test Users

**Option A: Using Seeder (Recommended)**
```bash
php artisan db:seed --class=DashboardDemoSeeder
```

This will create:
- 1 Admin user
- 2 Teacher users
- 3 Student users
- 4 Sample courses
- Multiple student enrollments

**Option B: Using Tinker**

Run `php artisan tinker` and execute:

```php
// Create Super Admin
$admin = \App\Models\User::create([
    'name' => 'Super Admin',
    'email' => 'admin@lms.com',
    'password' => bcrypt('password'),
    'national_id' => '1234567890',
    'role' => 'admin',
    'status' => 'active',
]);

// Create Teacher
$teacher = \App\Models\User::create([
    'name' => 'محمد السبيعي',
    'email' => 'teacher@lms.com',
    'password' => bcrypt('password'),
    'national_id' => '0987654321',
    'role' => 'teacher',
    'status' => 'active',
]);

// Create Student
$student = \App\Models\User::create([
    'name' => 'أحمد محمود',
    'email' => 'student@lms.com',
    'password' => bcrypt('password'),
    'national_id' => '1122334455',
    'role' => 'student',
    'status' => 'active',
]);

// Create Sample Courses
$course1 = \App\Models\Course::create([
    'title' => 'أساسيات قواعد البيانات',
    'description' => 'دورة عملية لتعلم أساسيات قواعد البيانات وكيفية إدارتها',
    'teacher_id' => $teacher->id,
    'status' => 'active',
    'start_date' => now(),
    'end_date' => now()->addMonths(3),
]);

$course2 = \App\Models\Course::create([
    'title' => 'تطوير تطبيقات الويب',
    'description' => 'تعلم بناء تطبيقات الويب باستخدام Laravel و Vue.js',
    'teacher_id' => $teacher->id,
    'status' => 'active',
    'start_date' => now(),
    'end_date' => now()->addMonths(4),
]);

// Enroll student in courses
\App\Models\Enrollment::create([
    'student_id' => $student->id,
    'course_id' => $course1->id,
    'status' => 'active',
    'enrolled_at' => now(),
    'progress' => 35,
]);

\App\Models\Enrollment::create([
    'student_id' => $student->id,
    'course_id' => $course2->id,
    'status' => 'active',
    'enrolled_at' => now(),
    'progress' => 20,
]);
```

### Step 3: Access Dashboards

1. **Start development server**:
   ```bash
   php artisan serve
   ```

2. **Login URLs**:
   - Admin: http://localhost:8000/login → admin@lms.com / password
   - Teacher: http://localhost:8000/login → teacher@lms.com / password
   - Student: http://localhost:8000/login → student@lms.com / password

3. **Dashboard URLs**:
   - Admin: http://localhost:8000/admin/dashboard
   - Teacher: http://localhost:8000/teacher/dashboard
   - Student: http://localhost:8000/student/dashboard

## Available Routes

### Admin Routes
```
GET  /admin/dashboard           ✅ Implemented
GET  /admin/teachers            🔜 To be implemented
GET  /admin/students            🔜 To be implemented
GET  /admin/courses             🔜 To be implemented
GET  /admin/reports             🔜 To be implemented
GET  /admin/settings            🔜 To be implemented
```

### Teacher Routes
```
GET  /teacher/dashboard         ✅ Implemented
GET  /teacher/courses           🔜 To be implemented
GET  /teacher/schedule          🔜 To be implemented
GET  /teacher/students          🔜 To be implemented
GET  /teacher/attendance        🔜 To be implemented
GET  /teacher/assignments       🔜 To be implemented
GET  /teacher/grades            🔜 To be implemented
GET  /teacher/profile           🔜 To be implemented
```

### Student Routes
```
GET  /student/dashboard         ✅ Implemented
GET  /student/courses           🔜 To be implemented
GET  /student/schedule          🔜 To be implemented
GET  /student/assignments       🔜 To be implemented
GET  /student/grades            🔜 To be implemented
GET  /student/attendance        🔜 To be implemented
GET  /student/profile           🔜 To be implemented
```

## Features Overview

### ✅ Implemented Features
- Role-based authentication
- Role-based dashboards (Admin, Teacher, Student)
- Responsive RTL Arabic design
- Dark mode support
- Statistics cards
- Course listing
- User profiles
- Dynamic navigation menus
- Logout functionality

### 🔜 Next Steps to Implement
1. **CRUD Operations**:
   - Teacher management (Admin)
   - Student management (Admin)
   - Course management (Admin & Teacher)

2. **Class Scheduling**:
   - Schedule creation
   - Calendar view
   - Class reminders

3. **Assignments System**:
   - Create assignments
   - Submit assignments
   - Grade assignments

4. **Attendance Tracking**:
   - Mark attendance
   - View attendance reports
   - Attendance analytics

5. **Grading System**:
   - Input grades
   - Calculate GPA
   - Grade reports

## Troubleshooting

### Assets not loading?
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Check if files exist
ls public/css/tailadmin.css
ls public/js/tailadmin.js
```

### Getting 403 Forbidden?
- Make sure user has correct role
- Check middleware in routes
- Verify user is authenticated

### Dashboard not showing data?
- Check if migrations are run
- Verify sample data is created
- Check console for JavaScript errors

## Customization

### Change Colors
Edit `public/css/tailadmin.css` and search for:
- `brand-500` - Primary color
- `success-500` - Success color
- `error-500` - Error color
- `warning-500` - Warning color

### Change Logo
Replace: `public/images/logo/logo.png`

### Modify Sidebar Menu
Edit role-specific sidebar files:
- `resources/views/layouts/partials/sidebar-admin.blade.php`
- `resources/views/layouts/partials/sidebar-teacher.blade.php`
- `resources/views/layouts/partials/sidebar-student.blade.php`

## Support

For issues or questions, refer to:
- Main documentation: `DASHBOARD_IMPLEMENTATION.md`
- Laravel documentation: https://laravel.com/docs
- TailAdmin documentation: https://tailadmin.com

## Login Credentials Summary

| Role     | Email              | Password | Dashboard URL                     |
|----------|-------------------|----------|-----------------------------------|
| Admin    | admin@lms.com     | password | /admin/dashboard                  |
| Teacher  | teacher@lms.com   | password | /teacher/dashboard                |
| Student  | student@lms.com   | password | /student/dashboard                |

---

**That's it! Your LMS dashboard system is ready to use! 🎉**
