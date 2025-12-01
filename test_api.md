# API Testing Guide - Quick Start

The server is now running at: **http://127.0.0.1:8000**

## Test the Authentication Flow

### 1. Register a Student

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

**Expected:** User created with status `pending`

---

### 2. Send OTP

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+966501234567",
    "type": "registration"
  }'
```

**Expected:** OTP sent (check logs for code)

**Get OTP from logs:**
```bash
# Windows
type storage\logs\laravel.log | findstr "OTP"
```

---

### 3. Verify OTP

Replace `123456` with actual OTP from logs:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+966501234567",
    "otp": "123456",
    "type": "registration"
  }'
```

**Expected:** Phone verified successfully

---

### 4. Initiate Nafath

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/nafath/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "national_id": "1234567890"
  }'
```

**Expected:** Transaction ID returned

Save the `transaction_id` from response!

---

### 5. Poll Nafath Status

Replace `{transaction_id}` with actual transaction ID:

```bash
curl http://127.0.0.1:8000/api/v1/auth/nafath/poll/{transaction_id}
```

**Poll 3-4 times** (simulates approval after 3 polls)

---

### 6. Login

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "password": "SecurePass123"
  }'
```

**Expected:** Token returned

Save the token!

---

### 7. Get Profile (Protected Endpoint)

Replace `{token}` with actual token:

```bash
curl http://127.0.0.1:8000/api/v1/profile \
  -H "Authorization: Bearer {token}"
```

**Expected:** User profile data

---

## Test Password Reset

### 1. Forgot Password

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com"
  }'
```

**Expected:** OTP sent to registered phone

---

### 2. Reset Password

Get OTP from logs, then:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+966501234567",
    "otp": "789012",
    "password": "NewPassword123",
    "password_confirmation": "NewPassword123"
  }'
```

**Expected:** Password reset successfully, all tokens revoked

---

### 3. Login with New Password

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "password": "NewPassword123"
  }'
```

**Expected:** New token generated

---

## Using Postman

### Import as Collection

1. Open Postman
2. Create new collection: "LMS API"
3. Set base URL variable: `http://127.0.0.1:8000/api/v1`
4. Add requests following the examples above

### Environment Variables

Create environment with:
- `base_url`: `http://127.0.0.1:8000/api/v1`
- `token`: (empty, will be set after login)
- `transaction_id`: (empty, will be set after Nafath initiate)

### Auto-Save Token Script

Add to Login request → Tests tab:

```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
    console.log("Token saved:", jsonData.data.token);
}
```

### Auto-Save Transaction ID Script

Add to Nafath Initiate → Tests tab:

```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.environment.set("transaction_id", jsonData.data.transaction.transaction_id);
    console.log("Transaction ID saved:", jsonData.data.transaction.transaction_id);
}
```

---

## Check Database

```sql
-- See all users
SELECT id, name, email, phone, role, status,
       phone_verified_at, nafath_verified_at
FROM users;

-- See OTP records
SELECT phone, otp, type, verified_at, expires_at, attempts
FROM otp_verifications
ORDER BY created_at DESC;

-- See Nafath transactions
SELECT transaction_id, national_id, status, created_at, completed_at
FROM nafath_transactions
ORDER BY created_at DESC;

-- See auth tokens
SELECT id, name, tokenable_id, created_at
FROM personal_access_tokens
ORDER BY created_at DESC;
```

---

## Troubleshooting

### Server not responding
- Check server is running: `http://127.0.0.1:8000`
- Restart server if needed

### OTP not found in logs
- Check: `storage/logs/laravel.log`
- Look for line: `local.INFO: OTP for +966501234567: 123456`

### Token not working
- Verify Bearer token format: `Authorization: Bearer {token}`
- Check token hasn't expired (30 days)
- Verify token exists in database

### Migration errors
- Run: `php artisan migrate:fresh`
- Clear cache: `php artisan cache:clear`

---

## API Response Format

**Success:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error (debug mode only)"
}
```

---

## Complete Documentation

- Registration Flow: [STUDENT_REGISTRATION_FLOW.md](STUDENT_REGISTRATION_FLOW.md)
- Password Reset: [PASSWORD_RESET_FLOW.md](PASSWORD_RESET_FLOW.md)
- Implementation Status: [LMS_IMPLEMENTATION_STATUS.md](LMS_IMPLEMENTATION_STATUS.md)
- Project Overview: [README.md](README.md)

---

**Server running at:** http://127.0.0.1:8000
**API Base URL:** http://127.0.0.1:8000/api/v1
