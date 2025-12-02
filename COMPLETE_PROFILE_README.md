# Complete Profile Endpoint Documentation

## Overview
The Complete Profile endpoint allows authenticated students to complete or update their profile information with all required personal data and document uploads.

---

## Endpoint Details

### **Complete Profile (Create/Update)**
- **URL**: `POST /api/v1/profile/complete`
- **Authentication**: Required (Bearer Token)
- **Content-Type**: `multipart/form-data`

---

## Request Parameters

### **Personal Information**

| Field | Type | Required | Validation | Description |
|-------|------|----------|------------|-------------|
| `name` | string | Yes | max:255 | Student's full name |
| `national_id` | string | Yes | 10 digits, unique | National ID number |
| `date_of_birth` | date | Yes | YYYY-MM-DD, before today | Date of birth |
| `gender` | enum | Yes | male, female | Gender |
| `email` | string | Yes | valid email, unique | Email address |
| `phone` | string | Yes | 10-20 digits, unique | Phone number (e.g., +966501234567) |
| `type` | enum | Yes | diploma, training | Program type |
| `program_id` | integer | Yes | exists in programs table | Selected program ID |
| `date_of_register` | date | No | YYYY-MM-DD | Registration date (defaults to today) |
| `is_terms` | boolean | Yes | must accept (1, true, yes, on) | Terms and conditions acceptance |
| `is_confirm_user` | boolean | Yes | must accept (1, true, yes, on) | Information confirmation |

### **Document Uploads**

| Field | Type | Required | Validation | Description |
|-------|------|----------|------------|-------------|
| `national_id_file` | file | Yes | PDF/JPG/PNG, max 5MB | National ID document |
| `certificate_file` | file | Yes | PDF/JPG/PNG, max 5MB | Academic certificate |
| `payment_billing_file` | file | Yes | PDF/JPG/PNG, max 5MB | Payment billing document |

---

## Example Request

### Using cURL

```bash
curl -X POST http://localhost:8000/api/v1/profile/complete \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json" \
  -F "name=Ahmed Ali Mohammed" \
  -F "national_id=1234567890" \
  -F "date_of_birth=1995-05-15" \
  -F "gender=male" \
  -F "email=ahmed.ali@example.com" \
  -F "phone=+966501234567" \
  -F "type=diploma" \
  -F "program_id=1" \
  -F "date_of_register=2025-12-02" \
  -F "is_terms=1" \
  -F "is_confirm_user=1" \
  -F "national_id_file=@/path/to/national_id.pdf" \
  -F "certificate_file=@/path/to/certificate.pdf" \
  -F "payment_billing_file=@/path/to/payment.pdf"
```

### Using JavaScript (Fetch API)

```javascript
const formData = new FormData();
formData.append('name', 'Ahmed Ali Mohammed');
formData.append('national_id', '1234567890');
formData.append('date_of_birth', '1995-05-15');
formData.append('gender', 'male');
formData.append('email', 'ahmed.ali@example.com');
formData.append('phone', '+966501234567');
formData.append('type', 'diploma');
formData.append('program_id', '1');
formData.append('date_of_register', '2025-12-02');
formData.append('is_terms', '1');
formData.append('is_confirm_user', '1');
formData.append('national_id_file', nationalIdFile);
formData.append('certificate_file', certificateFile);
formData.append('payment_billing_file', paymentFile);

fetch('http://localhost:8000/api/v1/profile/complete', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
    'Accept': 'application/json'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

---

## Response Examples

### Success Response (200 OK)

```json
{
  "data": {
    "id": 1,
    "name": "Ahmed Ali Mohammed",
    "email": "ahmed.ali@example.com",
    "phone": "+966501234567",
    "national_id": "1234567890",
    "date_of_birth": "1995-05-15",
    "gender": "male",
    "type": "diploma",
    "program_id": 1,
    "program": {
      "id": 1,
      "name": "Diploma in Information Technology",
      "code": "DIT-2025",
      "description": "Comprehensive diploma program covering IT fundamentals...",
      "type": "diploma",
      "duration_months": 24,
      "price": "15000.00",
      "status": "active",
      "created_at": "2025-12-02T10:00:00.000000Z",
      "updated_at": "2025-12-02T10:00:00.000000Z"
    },
    "date_of_register": "2025-12-02",
    "is_terms": true,
    "is_confirm_user": true,
    "role": "student",
    "status": "pending",
    "profile_photo": null,
    "bio": null,
    "email_verified_at": null,
    "phone_verified_at": "2025-12-02T09:00:00+00:00",
    "nafath_verified_at": "2025-12-02T09:30:00+00:00",
    "profile_completed_at": "2025-12-02T10:15:00+00:00",
    "documents": [
      {
        "id": 1,
        "user_id": 1,
        "document_type": "national_id",
        "file_path": "documents/1/national_id/1733140500_abc123.pdf",
        "original_name": "national_id.pdf",
        "file_size": 245678,
        "mime_type": "application/pdf",
        "status": "pending",
        "rejection_reason": null,
        "reviewed_by": null,
        "reviewed_at": null,
        "created_at": "2025-12-02T10:15:00.000000Z",
        "updated_at": "2025-12-02T10:15:00.000000Z"
      },
      {
        "id": 2,
        "user_id": 1,
        "document_type": "certificate",
        "file_path": "documents/1/certificate/1733140500_def456.pdf",
        "original_name": "certificate.pdf",
        "file_size": 345678,
        "mime_type": "application/pdf",
        "status": "pending",
        "rejection_reason": null,
        "reviewed_by": null,
        "reviewed_at": null,
        "created_at": "2025-12-02T10:15:00.000000Z",
        "updated_at": "2025-12-02T10:15:00.000000Z"
      },
      {
        "id": 3,
        "user_id": 1,
        "document_type": "payment_billing",
        "file_path": "documents/1/payment_billing/1733140500_ghi789.pdf",
        "original_name": "payment_receipt.pdf",
        "file_size": 123456,
        "mime_type": "application/pdf",
        "status": "pending",
        "rejection_reason": null,
        "reviewed_by": null,
        "reviewed_at": null,
        "created_at": "2025-12-02T10:15:00.000000Z",
        "updated_at": "2025-12-02T10:15:00.000000Z"
      }
    ],
    "created_at": "2025-12-02T08:00:00+00:00"
  }
}
```

### Validation Error Response (422 Unprocessable Entity)

```json
{
  "message": "The national id field must be exactly 10 digits. (and 3 more errors)",
  "errors": {
    "national_id": [
      "National ID must be exactly 10 digits."
    ],
    "is_terms": [
      "You must accept the terms and conditions."
    ],
    "national_id_file": [
      "The national id file field is required."
    ],
    "certificate_file": [
      "The certificate file field is required."
    ]
  }
}
```

### Server Error Response (500 Internal Server Error)

```json
{
  "message": "Failed to complete profile",
  "error": "Error details here"
}
```

---

## Available Programs Endpoint

### **Get All Programs**
- **URL**: `GET /api/v1/programs`
- **Authentication**: Not required
- **Query Parameters**:
  - `type` (optional): Filter by type (diploma or training)
  - `status` (optional): Filter by status (active or inactive, defaults to active)

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/programs?type=diploma" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Diploma in Information Technology",
      "code": "DIT-2025",
      "description": "Comprehensive diploma program covering IT fundamentals...",
      "type": "diploma",
      "duration_months": 24,
      "price": "15000.00",
      "status": "active",
      "created_at": "2025-12-02T10:00:00.000000Z",
      "updated_at": "2025-12-02T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Diploma in Business Administration",
      "code": "DBA-2025",
      "description": "Business management and administration diploma...",
      "type": "diploma",
      "duration_months": 18,
      "price": "12000.00",
      "status": "active",
      "created_at": "2025-12-02T10:00:00.000000Z",
      "updated_at": "2025-12-02T10:00:00.000000Z"
    }
  ]
}
```

---

## Database Schema

### Users Table (New Fields)
- `date_of_birth` (date, nullable)
- `gender` (enum: male, female, nullable)
- `type` (enum: diploma, training, nullable)
- `program_id` (foreign key to programs, nullable)
- `date_of_register` (date, nullable)
- `is_terms` (boolean, default: false)
- `is_confirm_user` (boolean, default: false)
- `profile_completed_at` (timestamp, nullable)

### Programs Table
- `id` (primary key)
- `name` (string)
- `code` (string, unique)
- `description` (text, nullable)
- `type` (enum: diploma, training)
- `duration_months` (integer, nullable)
- `price` (decimal 10,2, nullable)
- `status` (enum: active, inactive, default: active)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `deleted_at` (timestamp, nullable)

### Student Documents Table (Updated)
- Document types now include: `national_id`, `certificate`, `payment_billing`

---

## File Storage

Files are stored in: `storage/app/public/documents/{user_id}/{document_type}/`

File naming pattern: `{timestamp}_{unique_id}.{extension}`

To access uploaded files via URL, ensure you've run:
```bash
php artisan storage:link
```

Files will be accessible at: `http://localhost:8000/storage/documents/{user_id}/{document_type}/{filename}`

---

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Programs Table
```bash
php artisan db:seed --class=ProgramSeeder
```

### 3. Create Storage Link
```bash
php artisan storage:link
```

### 4. Import Postman Collection
Import the file: `postman/Complete_Profile_API.postman_collection.json`

---

## Workflow

1. **User Registration** → `POST /api/v1/auth/register`
2. **Phone Verification (OTP)** → `POST /api/v1/auth/send-otp` → `POST /api/v1/auth/verify-otp`
3. **National ID Verification (Nafath)** → `POST /api/v1/auth/nafath/initiate` → `GET /api/v1/auth/nafath/poll/{id}`
4. **Login** → `POST /api/v1/auth/login` (get access token)
5. **Get Available Programs** → `GET /api/v1/programs`
6. **Complete Profile** → `POST /api/v1/profile/complete` (with files)
7. **View Profile** → `GET /api/v1/profile`

---

## Features

✅ **CreateOrUpdate Logic**: Updates existing profile or creates new one
✅ **File Upload Handling**: Automatic file storage with unique naming
✅ **Transaction Safety**: Database rollback on errors
✅ **Validation**: Comprehensive field validation with custom messages
✅ **Document Management**: Replaces old documents when updating
✅ **Relationship Loading**: Returns user with program and documents
✅ **Timestamp Tracking**: `profile_completed_at` updated on each submission

---

## Models

- **[User](app/Models/User.php)**: Student model with profile fields
- **[Program](app/Models/Program.php)**: Available programs (diplomas/training)
- **[StudentDocument](app/Models/StudentDocument.php)**: Document storage and tracking

---

## Services

- **[DocumentUploadService](app/Services/DocumentUploadService.php)**: Handles file uploads, storage, and deletion

---

## Controllers

- **[ProfileController](app/Http/Controllers/Api/V1/ProfileController.php)**: Profile completion logic
- **[ProgramController](app/Http/Controllers/Api/V1/ProgramController.php)**: Programs listing

---

## Validation

- **[CompleteProfileRequest](app/Http/Requests/CompleteProfileRequest.php)**: Request validation with unique checks

---

## Testing with Postman

1. **Register a new user**
2. **Complete phone and Nafath verification**
3. **Login to get access token**
4. **Copy the access token** to Postman environment variable `access_token`
5. **Call GET /api/v1/programs** to see available programs
6. **Select a program_id** and prepare your files
7. **Call POST /api/v1/profile/complete** with all fields and files
8. **Verify response** includes updated user with program and documents

---

## Notes

- All file uploads are required on first submission
- On update, files are optional (only upload if you want to replace existing ones)
- Maximum file size: 5MB per file
- Accepted file formats: PDF, JPG, JPEG, PNG
- Phone and national_id must be unique across all users
- Terms and confirmation must be accepted (value: 1, true, yes, or on)
