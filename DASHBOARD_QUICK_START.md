# Quick Start Guide - Teacher Dashboard Configuration

## Installation Steps

### 1. Run Database Seeder
```bash
php artisan db:seed --class=DashboardViewSettingSeeder
```

This creates the `teacher_dashboard_view` setting in your database with the option to choose between:
- **teacher.dashboard** (Full View - Default)
- **teacher.dashboard-simple** (Simple View - New)

### 2. Clear Cache (Optional but Recommended)
```bash
php artisan cache:clear
```

## How to Use

### Change Dashboard View from Admin Settings
1. Login as Admin
2. Navigate to: **Admin Dashboard > Settings** or directly to `/admin/settings`
3. Look for the setting: **"نمط عرض لوحة المعلم"** (Teacher Dashboard View)
4. Select your preferred view:
   - العرض الكامل (Full View)
   - العرض المبسط (Simple View)
5. Click **Save**

Changes take effect immediately for all teachers.

## What's Available

### Full View (Default)
- **Route:** `http://127.0.0.1:8000/teacher/dashboard`
- **File:** `resources/views/teacher/dashboard.blade.php`
- **Features:** Complete statistics, charts, ratings, feedback, surveys

### Simple View
- **Route:** `http://127.0.0.1:8000/teacher/dashboard`
- **File:** `resources/views/teacher/dashboard-simple.blade.php`
- **Features:** Quick stats, clean layout, essential information only

## Files Changed

### Modified
- ✅ `app/Http/Controllers/Teacher/DashboardController.php`

### Created
- ✅ `resources/views/teacher/dashboard-simple.blade.php`
- ✅ `database/seeders/DashboardViewSettingSeeder.php`
- ✅ `app/Http/Controllers/Api/V1/Admin/DashboardViewController.php`
- ✅ `TEACHER_DASHBOARD_CONFIG.md` (Detailed guide)
- ✅ `DASHBOARD_CHANGES.md` (Complete documentation)

## Quick Test

1. Access teacher dashboard: `http://127.0.0.1:8000/teacher/dashboard`
2. Go to admin settings: `http://127.0.0.1:8000/admin/settings`
3. Change the dashboard view
4. Return to teacher dashboard - view should change

## API Usage

### Get Available Views
```bash
curl -H "Authorization: Bearer TOKEN" \
  http://127.0.0.1:8000/api/v1/admin/dashboard-views
```

### Update Dashboard View
```bash
curl -X POST http://127.0.0.1:8000/api/v1/admin/dashboard-view/teacher \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"view": "teacher.dashboard-simple"}'
```

## Programmatic Usage

```php
use App\Models\Setting;

// Get current setting
$view = Setting::get('teacher_dashboard_view', 'teacher.dashboard');

// Update setting
Setting::set('teacher_dashboard_view', 'teacher.dashboard-simple');

// Clear cache
Setting::clearCache();
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Setting not in admin UI | Run seeder: `php artisan db:seed --class=DashboardViewSettingSeeder` |
| View not changing | Clear cache: `php artisan cache:clear` |
| View not found error | Check file exists in `resources/views/teacher/` |

## Create Custom View

1. Create file: `resources/views/teacher/dashboard-myview.blade.php`
2. Extend base layout: `@extends('layouts.dashboard')`
3. Add to database setting options
4. Select from admin settings

## Documentation

- **Full Guide:** See `TEACHER_DASHBOARD_CONFIG.md`
- **Technical Details:** See `DASHBOARD_CHANGES.md`

## Support

For any issues:
1. Check documentation files
2. Verify seeder was run
3. Clear cache
4. Check database settings table
