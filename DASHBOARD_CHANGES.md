# Teacher Dashboard Configuration - Implementation Summary

## Overview

The teacher dashboard at `http://127.0.0.1:8000/teacher/dashboard` has been made fully configurable and extensible. You can now easily switch between different dashboard views and create custom ones.

## What Was Changed

### 1. **Controller Update**
**File:** `app/Http/Controllers/Teacher/DashboardController.php`

The `index()` method now:
- Fetches the dashboard view preference from the `Setting` model
- Falls back to `teacher.dashboard` if no setting is configured
- Validates that the view exists before rendering
- Passes all necessary data to any view

```php
// Get the preferred dashboard view from settings
// Default to 'teacher.dashboard' if not set
$dashboardView = \App\Models\Setting::get('teacher_dashboard_view', 'teacher.dashboard');

// Verify the view exists
if (!view()->exists($dashboardView)) {
    $dashboardView = 'teacher.dashboard';
}

return view($dashboardView, compact(...));
```

### 2. **New Dashboard Views**

#### Simple Dashboard View
**File:** `resources/views/teacher/dashboard-simple.blade.php` (NEW)

A lightweight, simplified dashboard with:
- Quick statistics cards
- Simplified subject listing
- Clean upcoming sessions display
- Support tickets
- Quick action links
- Minimal design for faster loading

### 3. **Database Seeder**
**File:** `database/seeders/DashboardViewSettingSeeder.php` (NEW)

Creates the `teacher_dashboard_view` setting in the database with options:
- `teacher.dashboard` - Full view
- `teacher.dashboard-simple` - Simple view

### 4. **API Endpoints for Settings**
**File:** `app/Http/Controllers/Api/V1/Admin/DashboardViewController.php` (NEW)

Provides RESTful API endpoints:
- `GET /api/v1/admin/dashboard-views` - Get available views
- `POST /api/v1/admin/dashboard-view/{role}` - Update dashboard view for a role

### 5. **API Routes**
**File:** `routes/api.php` (UPDATED)

Added routes for dashboard view management:
```php
Route::get('/dashboard-views', [DashboardViewController::class, 'getAvailableViews']);
Route::post('/dashboard-view/{role}', [DashboardViewController::class, 'updateDashboardView']);
```

### 6. **Documentation**
**File:** `TEACHER_DASHBOARD_CONFIG.md` (NEW)

Comprehensive guide for:
- Available views
- How to change views
- Creating custom views
- Data available to views
- Troubleshooting

## How to Use

### Step 1: Run the Seeder
```bash
php artisan db:seed --class=DashboardViewSettingSeeder
```

Or seed it with all seeders:
```bash
php artisan db:seed
```

### Step 2: Change Dashboard View (Admin)

**Option A: Using Admin Settings UI**
1. Go to `http://127.0.0.1:8000/admin/settings`
2. Find "نمط عرض لوحة المعلم" (Teacher Dashboard View)
3. Select preferred view and save

**Option B: Using API**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/admin/dashboard-view/teacher \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"view": "teacher.dashboard-simple"}'
```

**Option C: Database Query**
```php
\App\Models\Setting::set('teacher_dashboard_view', 'teacher.dashboard-simple');
```

### Step 3: Verify Changes
- Log in as a teacher
- Visit `http://127.0.0.1:8000/teacher/dashboard`
- The dashboard should display according to the selected view

## Available Views

| View Name | Key | File | Description |
|-----------|-----|------|-------------|
| Full View | `teacher.dashboard` | `resources/views/teacher/dashboard.blade.php` | Complete dashboard with all stats and charts |
| Simple View | `teacher.dashboard-simple` | `resources/views/teacher/dashboard-simple.blade.php` | Lightweight, simplified interface |

## Creating Custom Views

### 1. Create View File
Create `resources/views/teacher/dashboard-custom.blade.php`:
```blade
@extends('layouts.dashboard')

@section('title', 'Custom Teacher Dashboard')

@section('content')
<div class="container">
    <!-- Use $subjects, $stats, etc. -->
    {{ $subjects }}
</div>
@endsection
```

### 2. Add to Database
```sql
UPDATE settings 
SET options = '{"teacher.dashboard":"العرض الكامل","teacher.dashboard-simple":"العرض المبسط","teacher.dashboard-custom":"العرض المخصص"}'
WHERE key = 'teacher_dashboard_view';
```

### 3. Select from Settings
The new view will now appear in the admin settings dropdown.

## Data Available in All Views

All views have access to the following variables:

| Variable | Type | Purpose |
|----------|------|---------|
| `$subjects` | Collection | Teacher's assigned subjects |
| `$upcomingSessions` | Collection | Next 5 scheduled sessions |
| `$recentSessions` | Collection | Last 5 created sessions |
| `$stats` | Array | Aggregate statistics (counts) |
| `$teacherRating` | Array | Teacher ratings info (NELC 2.4.9) |
| `$recentFeedback` | Collection | Latest student feedback |
| `$ratingDistribution` | Collection | Ratings breakdown for charts |
| `$weeklyAttendance` | Collection | Weekly attendance stats |
| `$myTickets` | Collection | Last 5 support tickets |
| `$openTicketsCount` | Integer | Count of open tickets |
| `$pendingSurveys` | Integer | Count of pending surveys |

## API Endpoints

### Get Available Views
```
GET /api/v1/admin/dashboard-views
```

**Response:**
```json
{
  "available_views": {
    "teacher": [
      {
        "key": "teacher.dashboard",
        "label": "العرض الكامل",
        "description": "عرض شامل مع جميع الإحصائيات"
      },
      {
        "key": "teacher.dashboard-simple",
        "label": "العرض المبسط",
        "description": "عرض مبسط وسهل الاستخدام"
      }
    ]
  },
  "current_settings": {
    "teacher_dashboard_view": "teacher.dashboard"
  }
}
```

### Update Dashboard View
```
POST /api/v1/admin/dashboard-view/{role}
Content-Type: application/json

{
  "view": "teacher.dashboard-simple"
}
```

**Response:**
```json
{
  "message": "Dashboard view updated successfully",
  "setting_key": "teacher_dashboard_view",
  "new_value": "teacher.dashboard-simple"
}
```

## Testing

### Test 1: Verify Default Behavior
1. Access teacher dashboard
2. Should display full view by default

### Test 2: Change View via Admin
1. Go to admin settings
2. Change to simple view
3. Refresh teacher dashboard
4. Should show simple view

### Test 3: Verify Fallback
1. Manually set a non-existent view in database
2. Access teacher dashboard
3. Should fallback to full view

### Test 4: API Test
```bash
# Get available views
curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/v1/admin/dashboard-views

# Update view
curl -X POST http://localhost:8000/api/v1/admin/dashboard-view/teacher \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"view": "teacher.dashboard-simple"}'
```

## Cache Considerations

When updating settings via the API or database:
- Settings are cached using Laravel's cache system
- Admin settings page automatically clears cache
- If updating programmatically, call: `Setting::clearCache()`

```php
Setting::set('teacher_dashboard_view', 'teacher.dashboard-simple');
Setting::clearCache();
```

## Troubleshooting

### Dashboard Not Changing
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### View Not Found Error
- Verify view file exists in `resources/views/teacher/`
- Check file name matches exactly (case-sensitive)
- Verify it extends `layouts.dashboard`

### Setting Not Appearing in Admin UI
- Run seeder: `php artisan db:seed --class=DashboardViewSettingSeeder`
- Check that setting group is "dashboard"
- Verify `type` is "select"

## Future Enhancements

1. **Per-Teacher Preferences** - Allow each teacher to choose their own view
2. **More Views** - Create industry-specific, role-specific views
3. **Drag-and-Drop Builder** - Let admins create custom dashboards
4. **View Analytics** - Track which views are used most
5. **Scheduled View Changes** - Rotate views on a schedule
6. **Student/Admin Views** - Extend to other roles

## Files Summary

### Modified Files
- `app/Http/Controllers/Teacher/DashboardController.php` - Added dynamic view selection

### New Files
- `resources/views/teacher/dashboard-simple.blade.php` - New simple view
- `database/seeders/DashboardViewSettingSeeder.php` - Database seeder
- `app/Http/Controllers/Api/V1/Admin/DashboardViewController.php` - API controller
- `TEACHER_DASHBOARD_CONFIG.md` - Detailed configuration guide

### Updated Files
- `routes/api.php` - Added dashboard view API routes

## Version Info
- **Created:** 2024
- **Framework:** Laravel 11+
- **Tested:** PHP 8.1+

## Support

For issues or questions:
1. Check `TEACHER_DASHBOARD_CONFIG.md` for detailed guide
2. Review the controller implementation
3. Check database settings table for configuration
4. Clear cache and try again
