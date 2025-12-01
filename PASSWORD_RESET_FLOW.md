# Password Reset Flow - API Testing Guide

This document provides step-by-step instructions for testing the password reset workflow using Postman.

## Password Reset Workflow

The password reset process uses OTP verification to ensure security. Users receive a 6-digit code to their registered phone number.

---

## Step 1: Forgot Password (Request OTP)

**Endpoint:** `POST /api/v1/auth/forgot-password`

**Request Body:**
```json
{
  "email": "ahmed@example.com"
}
```

**Expected Response (200):**
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

**What Happens:**
1. System validates that email exists in database
2. Retrieves user's registered phone number
3. Generates 6-digit OTP (valid for 5 minutes)
4. Sends OTP to phone via SMS
5. Returns masked phone number for privacy

**Phone Number Masking:**
- For security, only last 4 digits are shown
- Example: `+966501234567` → `*********4567`
- This prevents exposing full phone numbers

**Check Logs for OTP:**
```bash
# Windows
type storage\logs\laravel.log | findstr "OTP"

# Linux/Mac
tail -f storage/logs/laravel.log | grep "OTP"
```

You'll see:
```
[2025-11-30 10:10:00] local.INFO: OTP for +966501234567: 789012
```

**Error Responses:**

```json
// Email not found
{
  "success": false,
  "message": "No account found with this email address."
}
```

```json
// No phone associated with account
{
  "success": false,
  "message": "No phone number associated with this account. Please contact support."
}
```

---

## Step 2: Reset Password (Verify OTP & Set New Password)

**Endpoint:** `POST /api/v1/auth/reset-password`

**Request Body:**
```json
{
  "phone": "+966501234567",
  "otp": "789012",
  "password": "NewSecurePass123",
  "password_confirmation": "NewSecurePass123"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Password reset successfully. Please login with your new password.",
  "data": {
    "email": "ahmed@example.com"
  }
}
```

**What Happens:**
1. OTP is verified (must be valid, not expired, attempts < 3)
2. User is found by phone number
3. Password is updated (hashed with bcrypt)
4. **All existing tokens are revoked** for security
5. User must login again with new password

**Security Note:**
When password is reset, ALL user sessions are invalidated by revoking all Sanctum tokens. This ensures:
- If account was compromised, attacker is logged out
- Only the legitimate user with the new password can access the account
- User must login again from all devices

**Password Requirements:**
- Minimum 8 characters
- Must contain letters
- Must contain numbers
- Must match confirmation

**Error Responses:**

```json
// Invalid or expired OTP
{
  "success": false,
  "message": "Invalid or expired OTP. Please request a new one."
}
```

```json
// Phone not found
{
  "success": false,
  "message": "No account found with this phone number."
}
```

```json
// Password validation failed
{
  "success": false,
  "message": "The password field must be at least 8 characters.",
  "errors": {
    "password": [
      "The password field must be at least 8 characters.",
      "The password field must contain letters.",
      "The password field must contain numbers."
    ]
  }
}
```

```json
// Password confirmation mismatch
{
  "success": false,
  "message": "Password confirmation does not match.",
  "errors": {
    "password": [
      "The password field confirmation does not match."
    ]
  }
}
```

---

## Step 3: Login with New Password

**Endpoint:** `POST /api/v1/auth/login`

**Request Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "NewSecurePass123"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "2|xyz789abc456def123ghi890jkl567mno234pqr901stu678vwx345yz",
    "user": {
      "id": 1,
      "name": "Ahmed Mohammed",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "role": "student",
      "status": "active"
    }
  }
}
```

**What Happens:**
- New token generated
- User can access protected endpoints again

---

## Complete Password Reset Flow Diagram

```
User Forgot Password
        ↓
POST /api/v1/auth/forgot-password
  { "email": "ahmed@example.com" }
        ↓
System validates email exists
        ↓
System finds user's phone number
        ↓
System generates 6-digit OTP
        ↓
OTP sent to phone (+966501234567)
        ↓
Response: "OTP sent to ******4567"
        ↓
User receives OTP: 789012
        ↓
POST /api/v1/auth/reset-password
  {
    "phone": "+966501234567",
    "otp": "789012",
    "password": "NewSecurePass123",
    "password_confirmation": "NewSecurePass123"
  }
        ↓
System verifies OTP
        ↓
System updates password (hashed)
        ↓
All tokens revoked (security)
        ↓
Response: "Password reset successfully"
        ↓
POST /api/v1/auth/login
  {
    "email": "ahmed@example.com",
    "password": "NewSecurePass123"
  }
        ↓
New token generated
        ↓
User logged in ✅
```

---

## Testing in Postman

### Test Sequence

1. **Forgot Password**
   - Enter email of existing user
   - Copy masked phone number from response
   - Check logs for actual OTP code

2. **Reset Password**
   - Enter full phone number (not masked)
   - Enter OTP from logs
   - Enter new password and confirmation
   - Verify success message

3. **Login with New Password**
   - Use new password to login
   - Save new token to environment
   - Old tokens are now invalid

### Postman Collection

**Forgot Password Request:**
```
POST {{base_url}}/auth/forgot-password
Content-Type: application/json

{
  "email": "ahmed@example.com"
}
```

**Reset Password Request:**
```
POST {{base_url}}/auth/reset-password
Content-Type: application/json

{
  "phone": "+966501234567",
  "otp": "789012",
  "password": "NewSecurePass123",
  "password_confirmation": "NewSecurePass123"
}
```

**Login Request:**
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
  "email": "ahmed@example.com",
  "password": "NewSecurePass123"
}
```

---

## OTP Security Features

### Expiration
- OTP valid for **5 minutes** only
- After expiration, user must request new OTP

### Attempt Limiting
- Maximum **3 verification attempts** per OTP
- After 3 failed attempts, OTP is invalidated
- User must request new OTP

### One-Time Use
- Each OTP can only be verified once
- After successful verification, OTP is marked as used
- Cannot reuse same OTP

### Type Isolation
- Password reset OTPs are type: `password_reset`
- Separate from registration OTPs (type: `registration`)
- Prevents OTP reuse across different flows

---

## Common Scenarios

### Scenario 1: OTP Expired
**Problem:** User took more than 5 minutes to enter OTP

**Solution:**
1. Request new OTP via forgot-password endpoint
2. Check logs for new OTP code
3. Enter new OTP within 5 minutes

### Scenario 2: Wrong OTP 3 Times
**Problem:** User entered wrong OTP 3 times

**Response:**
```json
{
  "success": false,
  "message": "Invalid or expired OTP. Please request a new one."
}
```

**Solution:**
1. Request new OTP via forgot-password endpoint
2. Be careful when entering the code

### Scenario 3: User Forgot Email
**Problem:** User doesn't remember which email they used

**Solution:**
- Contact support
- Admin can look up user by phone or national_id
- Provide email to user

### Scenario 4: No Phone Number on Account
**Problem:** Old account created before phone was required

**Response:**
```json
{
  "success": false,
  "message": "No phone number associated with this account. Please contact support."
}
```

**Solution:**
- Contact support
- Admin updates phone number manually
- Then password reset will work

---

## Security Considerations

### Token Revocation
When password is reset:
- **All user tokens are deleted** from database
- This logs out user from all devices/sessions
- Prevents unauthorized access if account was compromised
- User must login again with new password

### Phone Number Privacy
- Full phone number never returned in API responses
- Only last 4 digits shown (masked with asterisks)
- Example: `+966501234567` → `*********4567`
- Prevents phone number harvesting

### Password Validation
- Minimum 8 characters enforced
- Must contain both letters and numbers
- Uses Laravel's Password rule for strong validation
- Bcrypt hashing with cost factor 12

### OTP Logging
- OTP codes logged to `storage/logs/laravel.log`
- **Production:** Integrate with SMS provider (Unifonic/Twilio)
- **Development:** Check logs for testing
- Never expose OTP in API responses

---

## Database Verification

Check password reset progress:

```sql
-- Check OTP for password reset
SELECT phone, otp, type, verified_at, expires_at, attempts, created_at
FROM otp_verifications
WHERE phone = '+966501234567' AND type = 'password_reset'
ORDER BY created_at DESC
LIMIT 1;

-- Verify user password was updated
SELECT id, email, phone, updated_at
FROM users
WHERE email = 'ahmed@example.com';

-- Check tokens were revoked
SELECT COUNT(*) as active_tokens
FROM personal_access_tokens
WHERE tokenable_id = 1 AND tokenable_type = 'App\\Models\\User';
-- Should be 0 after password reset
```

---

## Integration with Registration Flow

Password reset uses the same OTP service as registration:

| Feature | Registration | Password Reset |
|---------|-------------|----------------|
| OTP Type | `registration` | `password_reset` |
| Phone Source | User input | Database lookup |
| OTP Expiry | 5 minutes | 5 minutes |
| Max Attempts | 3 | 3 |
| Token Action | Create new | Revoke all + create new on login |

Both flows use [app/Services/OtpService.php](app/Services/OtpService.php) for consistency.

---

## API Endpoints Summary

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `/api/v1/auth/forgot-password` | POST | No | Request password reset OTP |
| `/api/v1/auth/reset-password` | POST | No | Reset password with OTP |
| `/api/v1/auth/login` | POST | No | Login with new password |

---

## Troubleshooting

### Issue: "Email not found"
- Verify user exists in database
- Check email spelling
- Email is case-insensitive

### Issue: "Invalid OTP"
- Check logs for actual OTP code
- Verify OTP hasn't expired (5 min)
- Ensure correct phone number used
- Check OTP type is `password_reset`

### Issue: "Old token still works"
- Clear Postman cache
- Verify tokens were actually deleted from database
- Check using different token in Authorization header

### Issue: "Can't login with new password"
- Verify password meets requirements (8+ chars, letters, numbers)
- Check password was actually updated in database
- Try resetting password again

---

**Happy Testing!**

For registration flow, see [STUDENT_REGISTRATION_FLOW.md](STUDENT_REGISTRATION_FLOW.md)
