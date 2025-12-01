# Postman Collection - Import & Testing Guide

This guide shows you how to import and use the LMS API Postman collection for testing.

## Files Created

1. **LMS_API.postman_collection.json** - Complete API collection with all endpoints
2. **LMS_API.postman_environment.json** - Environment variables for local testing

---

## Import into Postman

### Step 1: Import Collection

1. Open Postman
2. Click **"Import"** button (top left)
3. Click **"Upload Files"** or drag and drop
4. Select **`LMS_API.postman_collection.json`**
5. Click **"Import"**

You should see a new collection: **"LMS API - Student Registration"**

### Step 2: Import Environment

1. Click **"Import"** button again
2. Select **`LMS_API.postman_environment.json`**
3. Click **"Import"**

You should see a new environment: **"LMS API - Local"**

### Step 3: Select Environment

1. In the top-right corner, click the environment dropdown
2. Select **"LMS API - Local"**

Now you're ready to test!

---

## Collection Structure

```
LMS API - Student Registration
├── Authentication
│   ├── 1. Register Student
│   ├── 2. Send OTP
│   ├── 3. Verify OTP
│   ├── 4. Initiate Nafath
│   ├── 5. Poll Nafath Status
│   ├── 6. Login
│   └── 7. Logout
├── Password Reset
│   ├── 1. Forgot Password
│   └── 2. Reset Password
└── Profile (Protected)
    └── Get Profile
```

---

## Environment Variables

The environment contains these variables:

| Variable | Initial Value | Auto-Set | Description |
|----------|---------------|----------|-------------|
| `base_url` | `http://127.0.0.1:8000/api/v1` | No | API base URL |
| `token` | (empty) | Yes | Auth token from login |
| `transaction_id` | (empty) | Yes | Nafath transaction ID |
| `user_id` | (empty) | Yes | User ID after registration |
| `user_email` | `ahmed@example.com` | Yes | User email |
| `user_phone` | `+966501234567` | Yes | User phone |

Variables marked "Yes" for Auto-Set are automatically updated by test scripts when you run requests.

---

## Testing the Complete Flow

### Prerequisites

**Server must be running:**
```bash
php artisan serve
```

Server should be at: `http://127.0.0.1:8000`

---

### Flow 1: Student Registration

Run these requests **in order**:

#### 1. Register Student

**Request:** POST `/auth/register`

**What it does:**
- Creates new user with `role=student` and `status=pending`
- Auto-saves user_id, user_email, user_phone to environment

**Expected Response:**
```json
{
  "success": true,
  "message": "Registration successful. Please verify your phone number.",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Mohammed",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "role": "student",
      "status": "pending"
    }
  }
}
```

**Console Output:**
```
User registered: { id: 1, name: "Ahmed Mohammed", ... }
```

---

#### 2. Send OTP

**Request:** POST `/auth/send-otp`

**Body uses:** `{{user_phone}}` variable

**What it does:**
- Generates 6-digit OTP
- Logs OTP to `storage/logs/laravel.log`
- In production: sends SMS

**Expected Response:**
```json
{
  "success": true,
  "message": "OTP sent successfully to +966501234567",
  "data": {
    "phone": "+966501234567",
    "expires_at": "2025-11-30T10:05:00.000000Z"
  }
}
```

**Console Output:**
```
OTP sent successfully!
Check storage/logs/laravel.log for OTP code
```

**Get OTP from logs:**
```bash
# Windows
type storage\logs\laravel.log | findstr "OTP"

# Linux/Mac
tail -f storage/logs/laravel.log | grep "OTP"
```

You'll see:
```
[2025-11-30 10:00:00] local.INFO: OTP for +966501234567: 123456
```

**Copy the 6-digit code (e.g., `123456`)**

---

#### 3. Verify OTP

**Request:** POST `/auth/verify-otp`

**IMPORTANT:** Replace `123456` in the request body with actual OTP from logs!

**Body:**
```json
{
  "phone": "{{user_phone}}",
  "otp": "123456",  // ← Replace with actual OTP
  "type": "registration"
}
```

**What it does:**
- Validates OTP (not expired, attempts < 3)
- Updates user's `phone_verified_at` timestamp

**Expected Response:**
```json
{
  "success": true,
  "message": "Phone number verified successfully.",
  "data": {
    "phone": "+966501234567",
    "verified": true
  }
}
```

**Console Output:**
```
Phone verified successfully!
```

---

#### 4. Initiate Nafath

**Request:** POST `/auth/nafath/initiate`

**What it does:**
- Creates Nafath verification transaction
- Returns transaction_id for polling
- Auto-saves transaction_id to environment

**Expected Response:**
```json
{
  "success": true,
  "message": "Nafath verification initiated. Please approve on your Nafath app.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "pending",
      "national_id": "1234567890",
      "created_at": "2025-11-30T10:10:00.000000Z"
    }
  }
}
```

**Console Output:**
```
Nafath initiated. Transaction ID: NFT-ABC123DEF456-1234567890
Poll the status 3-4 times for approval
```

---

#### 5. Poll Nafath Status

**Request:** GET `/auth/nafath/poll/{{transaction_id}}`

**What it does:**
- Checks Nafath verification status
- **Testing mode:** Auto-approves after 3 polls
- **Production:** Polls actual Nafath API

**How to use:**
1. Click "Send" on this request
2. Wait 3-5 seconds
3. Click "Send" again
4. Repeat 3-4 times until status is `approved`

**Expected Response (Pending):**
```json
{
  "success": true,
  "message": "Verification pending. Please approve on your Nafath app.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "pending",
      "polled_at": "2025-11-30T10:10:15.000000Z"
    }
  }
}
```

**Expected Response (Approved - after 3rd poll):**
```json
{
  "success": true,
  "message": "National ID verified successfully via Nafath.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "approved",
      "completed_at": "2025-11-30T10:10:45.000000Z"
    }
  }
}
```

**Console Output:**
```
Poll 1: ⏳ Still pending. Poll again in 3-5 seconds.
Poll 2: ⏳ Still pending. Poll again in 3-5 seconds.
Poll 3: ✅ Nafath verified! You can now login.
```

---

#### 6. Login

**Request:** POST `/auth/login`

**Body uses:** `{{user_email}}` variable

**What it does:**
- Authenticates user
- Generates 30-day token
- Auto-saves token to environment

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|abc123def456ghi789jkl012mno345pqr678",
    "user": {
      "id": 1,
      "name": "Ahmed Mohammed",
      "email": "ahmed@example.com",
      "role": "student",
      "status": "pending",
      "phone_verified_at": "2025-11-30T10:05:00.000000Z",
      "nafath_verified_at": "2025-11-30T10:10:45.000000Z"
    }
  }
}
```

**Console Output:**
```
✅ Login successful!
Token saved to environment: 1|abc123def456ghi789jkl012mno345pqr678
```

**Token is now available** as `{{token}}` in all protected requests!

---

#### 7. Get Profile (Protected)

**Request:** GET `/profile`

**Headers:**
- `Authorization: Bearer {{token}}` (automatically set)

**What it does:**
- Returns authenticated user data
- Tests that token is working

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Mohammed",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "role": "student",
      "status": "pending"
    }
  }
}
```

---

#### 8. Logout

**Request:** POST `/auth/logout`

**Headers:**
- `Authorization: Bearer {{token}}` (automatically set)

**What it does:**
- Revokes current token
- Auto-removes token from environment

**Expected Response:**
```json
{
  "success": true,
  "message": "Logged out successfully."
}
```

**Console Output:**
```
✅ Logged out successfully
```

---

### Flow 2: Password Reset

#### 1. Forgot Password

**Request:** POST `/auth/forgot-password`

**What it does:**
- Sends OTP to user's registered phone
- OTP type: `password_reset`

**Expected Response:**
```json
{
  "success": true,
  "message": "Password reset OTP sent to your registered phone number.",
  "data": {
    "phone": "******4567",
    "expires_at": "2025-11-30T10:15:00.000000Z"
  }
}
```

**Get OTP from logs** (same as registration OTP)

---

#### 2. Reset Password

**Request:** POST `/auth/reset-password`

**IMPORTANT:** Replace `789012` with actual OTP from logs!

**Body:**
```json
{
  "phone": "+966501234567",
  "otp": "789012",  // ← Replace with actual OTP
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

**What it does:**
- Validates OTP
- Updates password
- **Revokes ALL user tokens** (security measure)

**Expected Response:**
```json
{
  "success": true,
  "message": "Password reset successfully. Please login with your new password.",
  "data": {
    "email": "ahmed@example.com"
  }
}
```

**Now login again** with new password!

---

## Test Scripts Explained

Each request has automatic test scripts that:

### Register Student
```javascript
// Saves user data to environment
pm.environment.set("user_id", jsonData.data.user.id);
pm.environment.set("user_email", jsonData.data.user.email);
pm.environment.set("user_phone", jsonData.data.user.phone);
```

### Login
```javascript
// Saves auth token to environment
pm.environment.set("token", jsonData.data.token);
```

### Initiate Nafath
```javascript
// Saves transaction ID to environment
pm.environment.set("transaction_id", jsonData.data.transaction.transaction_id);
```

### Logout
```javascript
// Removes token from environment
pm.environment.unset("token");
```

These scripts make testing seamless - you don't need to manually copy/paste values!

---

## Viewing Environment Variables

**During testing:**
1. Click the **eye icon** (👁️) in top-right corner
2. See all current variable values
3. Verify token, transaction_id are set correctly

**To manually edit:**
1. Click environment name in top-right
2. Click **edit icon** (pencil)
3. Modify values
4. Save

---

## Troubleshooting

### Request Failed - Server Not Running
**Error:** `Could not get response`

**Solution:**
```bash
cd d:\mostaql\Lms
php artisan serve
```

Verify server is at: http://127.0.0.1:8000

---

### Token Not Working (401 Unauthorized)
**Error:** `Unauthenticated`

**Check:**
1. Token exists in environment (eye icon 👁️)
2. Token format: `Authorization: Bearer {{token}}`
3. Token hasn't expired (30 days)
4. You're logged in (run Login request)

**Fix:**
- Login again to get fresh token

---

### OTP Invalid or Expired
**Error:** `Invalid or expired OTP`

**Check:**
1. OTP is from logs (not making up numbers)
2. OTP hasn't expired (5 minutes)
3. Haven't exceeded 3 attempts
4. Using correct phone number

**Fix:**
- Request new OTP via "Send OTP"
- Check logs for new code

---

### Nafath Not Approving
**Status:** Always `pending`

**In testing mode:** Auto-approves after **3 polls**

**Fix:**
- Click "Send" on Poll request 3-4 times
- Wait 3-5 seconds between each poll
- Should approve on 3rd poll

---

### Variables Not Auto-Setting
**Check:**
1. Response status is 200/201 (successful)
2. Console shows variable saved messages
3. Test script is enabled (Scripts tab)

**Fix:**
- Check Postman Console (View → Show Postman Console)
- Look for script errors

---

## Advanced: Running Collection with Newman

Install Newman (Postman CLI):
```bash
npm install -g newman
```

Run entire collection:
```bash
newman run LMS_API.postman_collection.json -e LMS_API.postman_environment.json
```

Run with detailed output:
```bash
newman run LMS_API.postman_collection.json -e LMS_API.postman_environment.json --verbose
```

---

## Tips & Best Practices

### 1. Use Console for Debugging
- Open: View → Show Postman Console
- See all requests, responses, and script logs
- Track variable changes

### 2. Check Response Times
- Green = Good (< 200ms)
- Yellow = OK (200-1000ms)
- Red = Slow (> 1000ms)

### 3. Save Responses as Examples
- After successful request
- Click "Save Response"
- Choose "Save as example"
- Helps document expected responses

### 4. Organize with Folders
- Collection already organized by workflow
- Authentication → Password Reset → Protected

### 5. Test Error Cases
- Try invalid OTP
- Try expired token
- Try missing fields
- Verify error messages are helpful

---

## Next Steps

After completing registration flow:

1. **Document Upload** (coming soon)
   - Upload CV, certificates, national ID
   - Admin approval workflow

2. **Enrollment** (coming soon)
   - Browse programs
   - Enroll in terms
   - Select subjects

3. **Classes & Attendance** (coming soon)
   - Join Zoom classes
   - Track attendance
   - View recordings

---

## Additional Resources

- **Registration Flow:** [STUDENT_REGISTRATION_FLOW.md](STUDENT_REGISTRATION_FLOW.md)
- **Password Reset:** [PASSWORD_RESET_FLOW.md](PASSWORD_RESET_FLOW.md)
- **Quick Testing:** [test_api.md](test_api.md)
- **Project README:** [README.md](README.md)

---

**Happy Testing! 🚀**

For issues or questions, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```
