# Teacher Dashboard View Configuration

## Overview

The teacher dashboard at `http://127.0.0.1:8000/teacher/dashboard` is now configurable. You can easily switch between different dashboard views without modifying code.

## Available Views

### 1. **Full View** (`teacher.dashboard`)
- **View Name:** العرض الكامل
- **File:** `resources/views/teacher/dashboard.blade.php`
- **Features:**
  - Complete statistics (Subjects, Students, Sessions, Attendance Rate)
  - My Subjects section with detailed information
  - Recent student feedback and ratings (NELC 2.4.9)
  - Upcoming sessions in the sidebar
  - Support tickets management (NELC 1.3.3)
  - Pending surveys (NELC 1.2.11)
  - Weekly attendance charts
  - Rating distribution charts
  - Detailed feedback from students

### 2. **Simple View** (`teacher.dashboard-simple`)
- **View Name:** العرض المبسط
- **File:** `resources/views/teacher/dashboard-simple.blade.php`
- **Features:**
  - Quick statistics cards
  - My Subjects section (simplified)
  - Upcoming sessions (clean layout)
  - Support tickets
  - Quick action links
  - Light and minimal design

## How to Change the Dashboard View

### Method 1: Using Admin Settings (Recommended)

1. Navigate to: `http://127.0.0.1:8000/admin/settings`
2. Look for **"نمط عرض لوحة المعلم"** (Teacher Dashboard View) setting
3. Select your preferred view:
   - العرض الكامل (Full View)
   - العرض المبسط (Simple View)
4. Click **Save**

The change will apply immediately to all teachers.

### Method 2: Using Database Query

```php
// In Laravel Tinker or in a command
\App\Models\Setting::where('key', 'teacher_dashboard_view')
    ->update(['value' => 'teacher.dashboard-simple']);
```

### Method 3: Programmatically (In Code)

```php
use App\Models\Setting;

Setting::set('teacher_dashboard_view', 'teacher.dashboard-simple');
```

## How It Works

### Controller Changes

The `Teacher\DashboardController@index()` method now:

1. Gets the preferred dashboard view from the `Setting` model
2. Verifies that the view file exists
3. Falls back to the default `teacher.dashboard` view if the setting doesn't exist or view file is missing
4. Returns the selected view with all necessary data

```php
// Get the preferred dashboard view from settings
// Default to 'teacher.dashboard' if not set
$dashboardView = \App\Models\Setting::get('teacher_dashboard_view', 'teacher.dashboard');

// Verify the view exists
if (!view()->exists($dashboardView)) {
    $dashboardView = 'teacher.dashboard';
}

return view($dashboardView, compact(...data...));
```

### Database Setup

Run the seeder to create the setting in the database:

```bash
php artisan db:seed --class=DashboardViewSettingSeeder
```

Or add it manually to the `settings` table:

```sql
INSERT INTO settings (key, value, group, type, options, label, description, is_public)
VALUES (
    'teacher_dashboard_view',
    'teacher.dashboard',
    'dashboard',
    'select',
    '{"teacher.dashboard":"العرض الكامل","teacher.dashboard-simple":"العرض المبسط"}',
    'نمط عرض لوحة المعلم',
    'اختر نمط العرض المفضل لكل معلم: العرض الكامل بكل التفاصيل أو العرض المبسط',
    0
);
```

## Creating Custom Dashboard Views

To create your own dashboard view:

1. Create a new Blade template in `resources/views/teacher/dashboard-custom.blade.php`
2. Ensure it extends the layout: `@extends('layouts.dashboard')`
3. Use the same data variables passed by the controller:
   - `$subjects`
   - `$upcomingSessions`
   - `$recentSessions`
   - `$stats`
   - `$teacherRating`
   - `$recentFeedback`
   - `$ratingDistribution`
   - `$weeklyAttendance`
   - `$myTickets`
   - `$openTicketsCount`
   - `$pendingSurveys`

4. Add the view option to the setting in the database:

```sql
UPDATE settings 
SET options = '{"teacher.dashboard":"العرض الكامل","teacher.dashboard-simple":"العرض المبسط","teacher.dashboard-custom":"العرض المخصص"}'
WHERE key = 'teacher_dashboard_view';
```

5. Select it from admin settings

## Data Available in All Views

All dashboard views have access to the following data:

| Variable | Type | Description |
|----------|------|-------------|
| `$subjects` | Collection | Teacher's subjects with enrollment count |
| `$upcomingSessions` | Collection | Next 5 scheduled sessions |
| `$recentSessions` | Collection | Last 5 created sessions |
| `$stats` | Array | subjects_count, total_students, total_sessions, live_sessions |
| `$teacherRating` | Array | overall, breakdown, total_ratings |
| `$recentFeedback` | Collection | Last 5 approved student ratings |
| `$ratingDistribution` | Collection | Rating breakdown for charts |
| `$weeklyAttendance` | Collection | Weekly attendance data |
| `$myTickets` | Collection | Last 5 teacher tickets |
| `$openTicketsCount` | Integer | Count of open tickets |
| `$pendingSurveys` | Integer | Count of pending surveys |

## Adding View to Admin Settings UI

The setting should automatically appear in the admin settings page if you run the seeder. If you want to manually add it, edit the `dashboard` group settings in your database.

## Testing the Feature

1. Log in as a teacher
2. Visit: `http://127.0.0.1:8000/teacher/dashboard`
3. Go to admin settings and change the view
4. Return to the teacher dashboard - it should show the new view
5. Try both views and verify all data displays correctly

## Troubleshooting

### View Not Changing
- Clear the cache: `php artisan cache:clear`
- Verify the setting exists in the database
- Check that the view file exists in `resources/views/teacher/`

### Missing Data
- Ensure the teacher has subjects assigned
- Check the database for any relationship issues
- Verify all model relationships are properly configured

### Custom View Not Appearing
- Verify the view file path is correct
- Add the view option to the setting's `options` JSON
- Clear cache and try again

## Files Modified

1. **app/Http/Controllers/Teacher/DashboardController.php**
   - Updated the `index()` method to use dynamic view selection

2. **database/seeders/DashboardViewSettingSeeder.php** (New)
   - Seeder to add the setting to the database

3. **resources/views/teacher/dashboard-simple.blade.php** (New)
   - Alternative simplified dashboard view

## Future Enhancements

- Add per-teacher view preferences (allow each teacher to choose their own view)
- Create more specialized dashboard views (statistics-focused, schedule-focused, etc.)
- Add user preference settings in teacher profile
- Create drag-and-drop dashboard builder for custom layouts
