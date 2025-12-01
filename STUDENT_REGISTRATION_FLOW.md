# Student Registration Flow - API Testing Guide

This document provides step-by-step instructions for testing the complete student registration workflow using Postman.

## Complete Registration Workflow

### Step 1: Register Student

**Endpoint:** `POST /api/v1/auth/register`

**Request Body:**
```json
{
  "name": "Ahmed Mohammed",
  "email": "ahmed@example.com",
  "phone": "+966501234567",
  "national_id": "1234567890",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123"
}
```

**Expected Response (201):**
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
      "national_id": "1234567890",
      "role": "student",
      "status": "pending",
      "profile_photo": null,
      "bio": null,
      "email_verified_at": null,
      "phone_verified_at": null,
      "nafath_verified_at": null,
      "created_at": "2025-11-30T10:00:00.000000Z"
    }
  }
}
```

**What Happens:**
- User account created with `role='student'` and `status='pending'`
- User cannot login or enroll until verification is complete

---

### Step 2: Send OTP

**Endpoint:** `POST /api/v1/auth/send-otp`

**Request Body:**
```json
{
  "phone": "+966501234567",
  "type": "registration"
}
```

**Expected Response (200):**
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

**What Happens:**
- 6-digit OTP generated and stored in database
- OTP expires in 5 minutes
- SMS sent to phone (currently logs to `storage/logs/laravel.log`)
- Check logs to get OTP: `tail -f storage/logs/laravel.log`

**Check Logs for OTP:**
```bash
# On Windows
type storage\logs\laravel.log | findstr "OTP"

# On Linux/Mac
tail -f storage/logs/laravel.log | grep "OTP"
```

You'll see something like:
```
[2025-11-30 10:00:00] local.INFO: OTP for +966501234567: 123456
```

---

### Step 3: Verify OTP

**Endpoint:** `POST /api/v1/auth/verify-otp`

**Request Body:**
```json
{
  "phone": "+966501234567",
  "otp": "123456",
  "type": "registration"
}
```

**Expected Response (200):**
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

**What Happens:**
- OTP validated (must match, not expired, attempts < 3)
- User's `phone_verified_at` updated to current timestamp
- OTP marked as verified

**Error Responses:**
```json
// Invalid OTP
{
  "success": false,
  "message": "Invalid or expired OTP. Please try again."
}

// Max attempts reached
{
  "success": false,
  "message": "Invalid or expired OTP. Please try again."
}
```

---

### Step 4: Initiate Nafath Verification

**Endpoint:** `POST /api/v1/auth/nafath/initiate`

**Request Body:**
```json
{
  "national_id": "1234567890"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Nafath verification initiated. Please approve on your Nafath app.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "pending",
      "national_id": "1234567890",
      "created_at": "2025-11-30T10:10:00.000000Z",
      "polled_at": null,
      "completed_at": null
    },
    "instructions": "Open your Nafath mobile app and approve the verification request. Then poll the status using the transaction_id."
  }
}
```

**What Happens:**
- Unique transaction ID generated
- Transaction record created with `status='pending'`
- In production: API call to Nafath service
- User must approve on Nafath mobile app

**Save the `transaction_id` - you'll need it for polling!**

**Prerequisites Check:**
```json
// Phone not verified
{
  "success": false,
  "message": "Please verify your phone number first."
}

// Already verified
{
  "success": false,
  "message": "National ID already verified via Nafath."
}
```

---

### Step 5: Poll Nafath Status

**Endpoint:** `GET /api/v1/auth/nafath/poll/{transaction_id}`

**Example:** `GET /api/v1/auth/nafath/poll/NFT-ABC123DEF456-1234567890`

**Expected Response - Pending (200):**
```json
{
  "success": true,
  "message": "Verification pending. Please approve on your Nafath app.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "pending",
      "national_id": "1234567890",
      "created_at": "2025-11-30T10:10:00.000000Z",
      "polled_at": "2025-11-30T10:10:15.000000Z",
      "completed_at": null
    }
  }
}
```

**Expected Response - Approved (200):**
```json
{
  "success": true,
  "message": "National ID verified successfully via Nafath.",
  "data": {
    "transaction": {
      "transaction_id": "NFT-ABC123DEF456-1234567890",
      "status": "approved",
      "national_id": "1234567890",
      "created_at": "2025-11-30T10:10:00.000000Z",
      "polled_at": "2025-11-30T10:10:45.000000Z",
      "completed_at": "2025-11-30T10:10:45.000000Z"
    }
  }
}
```

**What Happens:**
- Poll every 3-5 seconds until status changes
- Currently: Auto-approves after 3 polls (for testing)
- In production: Actual Nafath API integration
- When approved: User's `nafath_verified_at` updated

**Polling Tips:**
- Poll every 3-5 seconds (not faster!)
- Timeout after 5 minutes (300 seconds)
- Stop polling when status is `approved`, `rejected`, or `expired`

---

### Step 6: Login

**Endpoint:** `POST /api/v1/auth/login`

**Request Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "SecurePass123"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
    "user": {
      "id": 1,
      "name": "Ahmed Mohammed",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "national_id": "1234567890",
      "role": "student",
      "status": "pending",
      "profile_photo": null,
      "bio": null,
      "email_verified_at": null,
      "phone_verified_at": "2025-11-30T10:05:00.000000Z",
      "nafath_verified_at": "2025-11-30T10:10:45.000000Z",
      "created_at": "2025-11-30T10:00:00.000000Z"
    }
  }
}
```

**What Happens:**
- User authenticated
- Sanctum token generated (valid for 30 days)
- User can now access protected endpoints

**Save the token!** Use it for all subsequent API calls:
```
Authorization: Bearer {token}
```

**Error Responses:**
```json
// Invalid credentials
{
  "success": false,
  "message": "Invalid credentials."
}

// Account suspended
{
  "success": false,
  "message": "Your account has been suspended. Please contact support."
}
```

---

### Step 7: Upload Documents (Coming Soon)

**Endpoint:** `POST /api/v1/student/documents`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
```
document_type: cv
file: [CV.pdf]
```

**Expected Documents:**
- CV (Curriculum Vitae)
- Academic Certificates
- National ID Copy
- Photo
- Other supporting documents

**What Happens:**
- File uploaded to storage
- Document record created with `status='pending'`
- Awaiting admin review

---

### Step 8: Admin Approval (Coming Soon)

**Admin approves documents:**
- Student's `status` changes from `pending` → `active`
- Student can now enroll in programs

---

## Testing Workflow in Postman

### Setup Collection

1. Create a new Postman collection: "LMS API"
2. Add environment variables:
   - `base_url`: `http://localhost:8000/api/v1`
   - `token`: (empty for now)
   - `transaction_id`: (empty for now)

### Test Sequence

1. **Register** → Copy user data
2. **Send OTP** → Check logs for OTP code
3. **Verify OTP** → Use OTP from logs
4. **Initiate Nafath** → Save `transaction_id` to environment
5. **Poll Nafath** (repeat 3-4 times) → Wait for `approved`
6. **Login** → Save `token` to environment
7. Use token for all protected endpoints

### Postman Scripts

**After Login - Save Token:**
```javascript
// In Login request → Tests tab
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```

**After Nafath Initiate - Save Transaction ID:**
```javascript
// In Initiate Nafath → Tests tab
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.environment.set("transaction_id", jsonData.data.transaction.transaction_id);
}
```

**Authorization Header (in protected routes):**
```
Authorization: Bearer {{token}}
```

---

## Current User Status Flow

```
Registration
    ↓
status: pending
phone_verified_at: null
nafath_verified_at: null
    ↓
Send OTP
    ↓
Verify OTP
    ↓
status: pending
phone_verified_at: [timestamp]
nafath_verified_at: null
    ↓
Initiate Nafath
    ↓
Poll Nafath (approved)
    ↓
status: pending
phone_verified_at: [timestamp]
nafath_verified_at: [timestamp]
    ↓
Upload Documents
    ↓
Admin Approves
    ↓
status: active ✅
Can now enroll in programs
```

---

## Troubleshooting

### OTP Not Working
- Check logs: `storage/logs/laravel.log`
- OTP expires in 5 minutes
- Max 3 attempts per OTP
- Request new OTP if expired

### Nafath Not Approving
- Currently auto-approves after 3 polls (for testing)
- Poll every 3-5 seconds
- Check transaction status in database

### Login Failed
- Verify email and password are correct
- Check user status (not suspended/rejected)
- User must exist in database

### Token Expired
- Tokens expire after 30 days
- Login again to get new token

---

## Database Verification

Check registration progress:

```sql
-- Check user
SELECT id, name, email, phone, national_id, role, status,
       phone_verified_at, nafath_verified_at
FROM users
WHERE email = 'ahmed@example.com';

-- Check OTP
SELECT phone, otp, type, verified_at, expires_at, attempts
FROM otp_verifications
WHERE phone = '+966501234567'
ORDER BY created_at DESC
LIMIT 1;

-- Check Nafath transaction
SELECT transaction_id, national_id, status, created_at, completed_at
FROM nafath_transactions
WHERE national_id = '1234567890'
ORDER BY created_at DESC
LIMIT 1;
```

---

## API Response Format

All API responses follow this structure:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful message",
  "data": {
    // Response data here
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error (only in debug mode)"
}
```

---

## Next Steps After Registration

Once student status is `active`:

1. Browse available programs
2. Enroll in a program
3. Select term subjects
4. Attend live classes (Zoom)
5. Submit assignments and exams
6. Receive evaluations and grades
7. Generate certificate upon completion

---

**Happy Testing!**
