# LMS API Setup Complete ✅

The Digital Learning Management System API is now fully functional and ready for testing!

## What's Been Fixed

### 1. API Routes Configuration
- **Problem:** Routes were not loading (404 errors)
- **Solution:** Added `api: __DIR__.'/../routes/api.php'` to [bootstrap/app.php](bootstrap/app.php:10)
- **Result:** All API endpoints now accessible at `http://127.0.0.1:8000/api/v1`

### 2. Database Migration Fixed
- **Problem:** MySQL error on `expires_at` field in OTP table
- **Solution:** Changed `expires_at` to nullable in migration
- **Result:** All migrations run successfully

### 3. OTP in API Response (Development Mode)
- **NEW FEATURE:** OTP code now included in API response when `APP_DEBUG=true`
- **Why:** Makes testing easier - no need to check logs
- **Where:** Send OTP and Forgot Password endpoints

## Server Status

✅ **Server Running:** http://127.0.0.1:8000
✅ **API Base URL:** http://127.0.0.1:8000/api/v1
✅ **Database:** Connected and migrated
✅ **Routes:** 10 API endpoints loaded

## Available Endpoints

### Public (No Authentication)
```
POST   /api/v1/auth/register          - Register new student
POST   /api/v1/auth/send-otp          - Send OTP to phone
POST   /api/v1/auth/verify-otp        - Verify OTP code
POST   /api/v1/auth/nafath/initiate   - Start Nafath verification
GET    /api/v1/auth/nafath/poll/{id}  - Poll Nafath status
POST   /api/v1/auth/login             - Login
POST   /api/v1/auth/forgot-password   - Request password reset OTP
POST   /api/v1/auth/reset-password    - Reset password with OTP
```

### Protected (Require Token)
```
POST   /api/v1/auth/logout            - Logout (revoke token)
GET    /api/v1/profile                - Get user profile
```

## Testing the API

### Quick Test - Register Flow

**1. Register Student:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed Mohammed",
    "email": "ahmed@example.com",
    "phone": "+966501234567",
    "national_id": "1234567890",
    "password": "SecurePass123",
    "password_confirmation": "SecurePass123"
  }'
```

**2. Send OTP:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+966501234567",
    "type": "registration"
  }'
```

**Response (with OTP included):**
```json
{
  "success": true,
  "message": "OTP sent successfully to +966501234567",
  "data": {
    "phone": "+966501234567",
    "expires_at": "2025-11-30T18:00:00.000000Z",
    "otp": "123456"  ← OTP code included in debug mode!
  }
}
```

**3. Verify OTP (use OTP from response):**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+966501234567",
    "otp": "123456",
    "type": "registration"
  }'
```

**4. Continue with Nafath and Login...**

## Using Postman

### Import Collection
1. Open Postman
2. Import [LMS_API.postman_collection.json](LMS_API.postman_collection.json)
3. Import [LMS_API.postman_environment.json](LMS_API.postman_environment.json)
4. Select "LMS API - Local" environment
5. Start testing!

### Key Benefits
- ✅ **OTP in response** - No need to check logs!
- ✅ **Auto-save variables** - Token, transaction_id saved automatically
- ✅ **Console logging** - See helpful messages as you test
- ✅ **Complete workflow** - All 10 endpoints ready to test

## OTP Code in Response

### How it Works

When `APP_DEBUG=true` (default in development):
- OTP code **included** in Send OTP response
- OTP code **included** in Forgot Password response
- Makes testing super easy!

When `APP_DEBUG=false` (production):
- OTP code **hidden** from response
- OTP only sent via SMS
- Secure for production use

### Example Response with OTP

**Development (APP_DEBUG=true):**
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "phone": "+966501234567",
    "expires_at": "2025-11-30T18:00:00.000000Z",
    "otp": "654321"  ← Included for testing!
  }
}
```

**Production (APP_DEBUG=false):**
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "phone": "+966501234567",
    "expires_at": "2025-11-30T18:00:00.000000Z"
    // No OTP field - secure!
  }
}
```

## Environment Configuration

Check your `.env` file:

```env
APP_DEBUG=true  ← Must be true to see OTP in response

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=your_password

SANCTUM_TOKEN_EXPIRATION=43200  # 30 days
```

## Complete Documentation

| Document | Description |
|----------|-------------|
| [README.md](README.md) | Project overview and setup |
| [STUDENT_REGISTRATION_FLOW.md](STUDENT_REGISTRATION_FLOW.md) | Detailed registration workflow |
| [PASSWORD_RESET_FLOW.md](PASSWORD_RESET_FLOW.md) | Password reset guide |
| [POSTMAN_GUIDE.md](POSTMAN_GUIDE.md) | Postman collection usage |
| [test_api.md](test_api.md) | Quick API testing guide |
| [LMS_IMPLEMENTATION_STATUS.md](LMS_IMPLEMENTATION_STATUS.md) | Implementation progress |

## What's Next?

The authentication system is complete! Next features to implement:

### Phase 2 - Document Upload (Next)
- Student document upload controller
- File storage (local/S3)
- Admin document review endpoints
- Document approval workflow

### Phase 3 - Academic Structure
- Programs, Terms, Subjects models
- CRUD controllers for academic entities
- Enrollment system
- Teacher assignment

### Phase 4 - Classes & Attendance
- Zoom integration
- Attendance tracking
- Live and recorded lectures

### Phase 5 - Exams & Evaluations
- Exam creation and management
- Student submissions
- Grading system
- Evaluation aggregation

## Testing Checklist

- [ ] Register new student
- [ ] Send OTP (verify OTP in response)
- [ ] Verify OTP
- [ ] Initiate Nafath verification
- [ ] Poll Nafath status (3-4 times)
- [ ] Login and receive token
- [ ] Get profile (protected endpoint)
- [ ] Forgot password (verify OTP in response)
- [ ] Reset password with OTP
- [ ] Login with new password
- [ ] Logout

## Troubleshooting

### OTP not in response?
- Check `APP_DEBUG=true` in `.env`
- Restart server: `php artisan serve`

### Route not found?
- Verify server running: http://127.0.0.1:8000
- Check routes loaded: `php artisan route:list --path=api/v1`

### Token not working?
- Ensure using `Authorization: Bearer {token}`
- Check token saved in Postman environment
- Login again to get fresh token

### Database errors?
- Run: `php artisan migrate:fresh`
- Check MySQL connection in `.env`

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Review error messages in Postman
- Verify request body format (JSON)
- Check database has data

---

**🎉 Authentication System Complete!**

The API is ready for testing. Import the Postman collection and start testing the complete student registration flow!

**Server:** http://127.0.0.1:8000
**API:** http://127.0.0.1:8000/api/v1
**Status:** ✅ Running
