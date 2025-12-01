# Digital Learning Management System (LMS) API

A comprehensive backend API for a Saudi Arabia-focused Digital Learning Platform supporting 2-year diploma programs with Nafath identity verification, multi-role authorization, and integrated payment systems.

## Project Overview

This LMS provides complete backend infrastructure for managing:
- **Academic Programs**: 2-year diploma programs with term-based structure
- **Multi-Role System**: Students, Teachers, Admins, and Super Admins
- **Saudi Identity Verification**: Nafath OpenAccount integration with polling
- **Payment Integration**: Apple Pay, Tamara (BNPL), and Cash payments
- **Live & Recorded Lectures**: Zoom integration with attendance tracking
- **Exam & Evaluation System**: Comprehensive assessment management
- **Support System**: Ticketing and 1:1 messaging
- **Content Management**: Blog and community features

## Technology Stack

- **Framework**: Laravel 12.0
- **PHP Version**: 8.2
- **Authentication**: Laravel Sanctum (Token-based)
- **Database**: SQLite (Development), MySQL (Production)
- **API Version**: v1
- **Token Expiration**: 30 days

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- SQLite or MySQL

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd Lms

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`

## Implementation Status

### Phase 1: Foundation & Authentication (50% Complete)

#### Completed
- ✅ Laravel 12 installation with Sanctum
- ✅ User model enhancement with roles and verification fields
- ✅ OTP verification system (model, migration)
- ✅ Nafath transaction tracking (model, migration)
- ✅ Student document upload system (model, migration)
- ✅ Complete API route structure (150+ endpoints defined)
- ✅ Database migrations (5 tables operational)

#### In Progress
- 🔄 Service classes (OtpService, NafathService)
- 🔄 Authentication controllers
- 🔄 Request validation classes
- 🔄 API resource formatters

#### Pending
- ⏳ Middleware for role-based access
- ⏳ File upload implementation
- ⏳ SMS integration (Unifonic/Twilio)
- ⏳ Actual Nafath API integration

## Database Schema

### Current Tables (5)

1. **users** - Core user management
   - Standard fields: id, name, email, password
   - LMS fields: phone, national_id, role, status
   - Verification: email_verified_at, phone_verified_at, nafath_verified_at
   - Profile: profile_photo, bio
   - Soft deletes enabled

2. **otp_verifications** - OTP verification tracking
   - Fields: phone, otp, type, verified_at, expires_at, attempts
   - 5-minute expiration, 3 max attempts

3. **nafath_transactions** - Nafath identity verification
   - Fields: user_id, transaction_id, national_id, status
   - Polling support: polled_at, request/response payloads
   - Statuses: pending, approved, rejected, expired

4. **student_documents** - Document upload & approval
   - Fields: user_id, document_type, file_path, status
   - Review tracking: reviewed_by, reviewed_at, rejection_reason
   - Types: cv, academic_certificate, national_id, photo, other
   - Soft deletes enabled

5. **personal_access_tokens** - Sanctum authentication tokens

### Planned Tables (28 more)
Programs, Terms, Subjects, Classes, Enrollments, Attendance, Exams, Questions, Submissions, Evaluations, Payments, Certificates, Messages, Tickets, Posts, Comments, and more.

## API Endpoints

### Authentication (Public)
```
POST   /api/v1/auth/register              - Register new user
POST   /api/v1/auth/send-otp              - Send OTP to phone
POST   /api/v1/auth/verify-otp            - Verify OTP code
POST   /api/v1/auth/nafath/initiate       - Start Nafath verification
GET    /api/v1/auth/nafath/poll/{id}      - Poll Nafath status
POST   /api/v1/auth/login                 - Login with credentials
POST   /api/v1/auth/forgot-password       - Request password reset
POST   /api/v1/auth/reset-password        - Reset password with token
```

### Protected Endpoints (Require Authentication)

All protected routes require `Authorization: Bearer {token}` header.

#### Profile Management
```
GET    /api/v1/profile                    - Get current user profile
PUT    /api/v1/profile                    - Update profile
POST   /api/v1/profile/photo              - Upload profile photo
POST   /api/v1/auth/logout                - Logout (revoke token)
```

#### Student Documents
```
GET    /api/v1/student/documents          - List uploaded documents
POST   /api/v1/student/documents          - Upload new document
GET    /api/v1/student/documents/{id}     - View document details
DELETE /api/v1/student/documents/{id}     - Delete document
```

#### Admin - Document Review
```
GET    /api/v1/admin/documents/pending    - List pending documents
POST   /api/v1/admin/documents/{id}/approve  - Approve document
POST   /api/v1/admin/documents/{id}/reject   - Reject with reason
```

**150+ additional endpoints** planned for Programs, Terms, Subjects, Classes, Enrollment, Attendance, Exams, Payments, Messages, Tickets, Blog, etc.

## Student Registration Workflow

1. **Register** → POST `/api/v1/auth/register`
   - Provide: name, phone, national_id, email, password
   - User created with role='student', status='pending'

2. **Send OTP** → POST `/api/v1/auth/send-otp`
   - OTP sent to phone via SMS
   - Valid for 5 minutes, max 3 attempts

3. **Verify OTP** → POST `/api/v1/auth/verify-otp`
   - User phone_verified_at updated

4. **Initiate Nafath** → POST `/api/v1/auth/nafath/initiate`
   - Returns transaction_id for polling
   - User must approve on Nafath app

5. **Poll Nafath Status** → GET `/api/v1/auth/nafath/poll/{transaction_id}`
   - Poll every 3-5 seconds
   - When approved: nafath_verified_at updated

6. **Login** → POST `/api/v1/auth/login`
   - Receive authentication token

7. **Upload Documents** → POST `/api/v1/student/documents`
   - Upload: CV, academic certificates, national ID, photo
   - Documents status='pending'

8. **Admin Approval** → Admin reviews and approves
   - Admin uses: POST `/api/v1/admin/documents/{id}/approve`
   - User status changes from 'pending' → 'active'

9. **Enrollment** → Student can now enroll in programs

## User Roles & Permissions

### Student
- Register and complete verification
- Upload required documents
- Enroll in programs and subjects
- Attend classes and view recordings
- Submit assignments and exams
- View grades and evaluations
- Send messages to teachers
- Create support tickets
- Access blog and community

### Teacher
- Manage assigned subjects
- Create and schedule classes (Zoom)
- Take attendance based on Zoom login/interaction
- Create exams and questions
- Grade submissions
- Provide evaluations
- Message enrolled students
- Post blog content

### Admin
- Review and approve student documents
- Manage programs, terms, subjects
- Assign teachers to subjects
- Process payments (cash confirmations)
- Handle support tickets
- Manage blog posts
- View all system data

### Super Admin
- All admin permissions
- User management (create, suspend, delete)
- System configuration
- Accreditation management
- Certificate generation
- Access logs and analytics

## Authentication Flow

### Registration
```json
POST /api/v1/auth/register
{
  "name": "Ahmed Mohammed",
  "phone": "+966501234567",
  "national_id": "1234567890",
  "email": "ahmed@example.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123"
}

Response:
{
  "message": "Registration successful. Please verify your phone.",
  "user": {
    "id": 1,
    "name": "Ahmed Mohammed",
    "email": "ahmed@example.com",
    "phone": "+966501234567",
    "role": "student",
    "status": "pending"
  }
}
```

### Login
```json
POST /api/v1/auth/login
{
  "email": "ahmed@example.com",
  "password": "SecurePass123"
}

Response:
{
  "token": "1|abc123def456...",
  "user": {
    "id": 1,
    "name": "Ahmed Mohammed",
    "role": "student",
    "status": "active"
  }
}
```

### Using Token
```
GET /api/v1/profile
Authorization: Bearer 1|abc123def456...

Response:
{
  "id": 1,
  "name": "Ahmed Mohammed",
  "email": "ahmed@example.com",
  "phone": "+966501234567",
  "role": "student",
  "status": "active",
  "email_verified_at": "2025-11-29T10:30:00.000000Z",
  "phone_verified_at": "2025-11-29T10:32:00.000000Z",
  "nafath_verified_at": "2025-11-29T10:35:00.000000Z"
}
```

## Testing with Postman

### Setup
1. Import the API collection (once available)
2. Set base URL: `http://localhost:8000/api/v1`
3. Create environment variables:
   - `base_url`: `http://localhost:8000/api/v1`
   - `token`: (will be set after login)

### Test Flow
1. Register a new user
2. Send OTP (currently mock - will integrate SMS)
3. Verify OTP
4. Initiate Nafath (currently returns mock transaction)
5. Poll Nafath status
6. Login and save token to environment
7. Test protected endpoints with token

## Security Features

- **Token Authentication**: 30-day expiring tokens via Sanctum
- **Password Hashing**: Bcrypt with configurable rounds
- **OTP Security**: 5-minute expiration, 3 max attempts
- **Role-Based Access**: Middleware enforces permissions
- **Soft Deletes**: Data protection through logical deletion
- **SQL Injection Protection**: Eloquent ORM parameterized queries
- **CSRF Protection**: Laravel default protection
- **Rate Limiting**: API throttling (configured)
- **Input Validation**: Request validation classes
- **File Upload Validation**: MIME type and size restrictions

## Environment Configuration

Key `.env` variables:

```env
# Application
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lms_db
# DB_USERNAME=root
# DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_TOKEN_EXPIRATION=43200  # 30 days

# LMS Settings
LMS_DEFAULT_CURRENCY=SAR
LMS_TAX_PERCENTAGE=15
LMS_MIN_ATTENDANCE_PERCENTAGE=75

# SMS (To be configured)
# SMS_PROVIDER=unifonic
# SMS_API_KEY=
# SMS_SENDER_ID=

# Nafath (To be configured)
# NAFATH_API_URL=
# NAFATH_API_KEY=
# NAFATH_TIMEOUT=300

# Payment Gateways (To be configured)
# APPLE_PAY_MERCHANT_ID=
# TAMARA_API_KEY=
# TAMARA_API_URL=

# Zoom (To be configured)
# ZOOM_API_KEY=
# ZOOM_API_SECRET=
```

## Next Steps

### Immediate (Week 1-2)
1. Create OtpService for SMS integration
2. Create NafathService for API integration
3. Build authentication controllers
4. Implement request validation
5. Create API resources for response formatting
6. Add role-based middleware
7. Implement file upload system

### Short-term (Week 3-4)
8. Academic structure (Programs, Terms, Subjects)
9. Enrollment system
10. Teacher assignment

### Medium-term (Week 5-8)
11. Zoom integration for classes
12. Attendance tracking system
13. Exam creation and submission
14. Evaluation system

### Long-term (Week 9-18)
15. Payment gateway integration
16. Calendar and scheduling
17. Messaging system
18. Ticket support system
19. Blog and community features
20. Certificate generation
21. Analytics and reporting

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/
│   │       ├── Auth/           # Authentication controllers
│   │       ├── Student/        # Student-specific controllers
│   │       ├── Teacher/        # Teacher-specific controllers
│   │       └── Admin/          # Admin controllers
│   ├── Middleware/             # Custom middleware
│   ├── Requests/               # Form request validation
│   └── Resources/              # API response resources
├── Models/                     # Eloquent models
│   ├── User.php
│   ├── OtpVerification.php
│   ├── NafathTransaction.php
│   └── StudentDocument.php
├── Services/                   # Business logic services
│   ├── OtpService.php
│   └── NafathService.php
└── Exceptions/                 # Custom exceptions

database/
├── migrations/                 # Database migrations
└── seeders/                    # Database seeders

routes/
└── api.php                     # API routes (v1)

config/
├── sanctum.php                 # Authentication config
└── lms.php                     # LMS-specific config (planned)
```

## Support & Documentation

- **Implementation Status**: See [LMS_IMPLEMENTATION_STATUS.md](LMS_IMPLEMENTATION_STATUS.md)
- **API Documentation**: Postman collection (coming soon)
- **Database Schema**: See migrations in `database/migrations/`
- **Troubleshooting**: Check Laravel logs in `storage/logs/`

## License

This project is proprietary software developed for educational purposes.

---

**Current Version**: 0.1.0 (Phase 1 - 50% Complete)
**Last Updated**: 2025-11-30
**Framework**: Laravel 12.0
**PHP**: 8.2
