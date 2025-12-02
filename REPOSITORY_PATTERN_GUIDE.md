# Repository Pattern Implementation Guide

## Overview
This LMS uses the **Repository Pattern** to abstract data access logic and provide a clean separation between business logic and data persistence.

## Architecture

```
Controller → Service → Repository → Model → Database
```

### Benefits
- ✅ **Separation of Concerns**: Controllers don't directly interact with models
- ✅ **Testability**: Easy to mock repositories in tests
- ✅ **Maintainability**: Changes to data access logic are centralized
- ✅ **Flexibility**: Can switch data sources without changing business logic
- ✅ **Reusability**: Common queries are encapsulated in repository methods

## Directory Structure

```
app/
├── Repositories/
│   ├── Contracts/              # Repository Interfaces
│   │   ├── BaseRepositoryInterface.php
│   │   ├── TermRepositoryInterface.php
│   │   ├── SubjectRepositoryInterface.php
│   │   ├── EnrollmentRepositoryInterface.php
│   │   ├── SessionRepositoryInterface.php
│   │   ├── AttendanceRepositoryInterface.php
│   │   └── EvaluationRepositoryInterface.php
│   │
│   └── Eloquent/              # Eloquent Implementations
│       ├── BaseRepository.php
│       ├── TermRepository.php
│       ├── SubjectRepository.php
│       ├── EnrollmentRepository.php
│       ├── SessionRepository.php
│       ├── AttendanceRepository.php
│       └── EvaluationRepository.php
│
├── Services/                  # Business Logic Layer
│   ├── TermService.php
│   ├── SubjectService.php
│   ├── EnrollmentService.php
│   ├── SessionService.php
│   ├── AttendanceService.php
│   ├── EvaluationService.php
│   ├── VideoUploadService.php
│   └── GradeCalculationService.php
│
└── Http/Controllers/Api/V1/  # HTTP Request Handlers
    ├── TermController.php
    ├── SubjectController.php
    ├── EnrollmentController.php
    ├── SessionController.php
    ├── AttendanceController.php
    └── EvaluationController.php
```

## Usage Examples

### 1. Basic Repository Usage

```php
// In Controller
public function __construct(
    private TermRepositoryInterface $termRepository
) {}

public function index()
{
    // Get all active terms with program relationship
    $terms = $this->termRepository->all(
        columns: ['*'],
        relations: ['program']
    );

    return TermResource::collection($terms);
}
```

### 2. Service Layer Usage

```php
// TermService.php
class TermService
{
    public function __construct(
        private TermRepositoryInterface $termRepository
    ) {}

    public function getActiveTerm(int $programId): ?Term
    {
        return $this->termRepository->getCurrentActiveTerm($programId);
    }

    public function autoAssignStudent(User $student): ?Term
    {
        return $this->termRepository->autoAssignStudentToTerm($student);
    }
}
```

### 3. Controller with Service

```php
// TermController.php
class TermController extends Controller
{
    public function __construct(
        private TermService $termService
    ) {}

    public function assignStudent(Request $request)
    {
        $term = $this->termService->autoAssignStudent($request->user());

        return new TermResource($term);
    }
}
```

## Base Repository Methods

All repositories inherit these methods from `BaseRepository`:

| Method | Parameters | Description |
|--------|-----------|-------------|
| `all()` | `$columns`, `$relations` | Get all records |
| `paginate()` | `$perPage`, `$columns`, `$relations` | Get paginated records |
| `find()` | `$id`, `$columns`, `$relations` | Find by ID |
| `findOrFail()` | `$id`, `$columns`, `$relations` | Find by ID or throw exception |
| `findBy()` | `$column`, `$value`, `$columns`, `$relations` | Find by column |
| `findAllBy()` | `$column`, `$value`, `$columns`, `$relations` | Find all by column |
| `create()` | `$data` | Create new record |
| `update()` | `$id`, `$data` | Update record |
| `delete()` | `$id` | Delete record |
| `where()` | `$conditions`, `$columns`, `$relations` | Query with conditions |
| `firstWhere()` | `$conditions`, `$columns`, `$relations` | First record matching conditions |
| `count()` | `$conditions` | Count records |
| `exists()` | `$conditions` | Check if record exists |

## Custom Repository Methods

Each repository can define custom methods for specific business needs:

### TermRepository
- `getActiveTerms()` - Get all active terms
- `getByProgram($programId)` - Get terms for specific program
- `getCurrentActiveTerm($programId)` - Get current active term
- `getNextUpcomingTerm($programId)` - Get next upcoming term
- `isRegistrationOpen($termId)` - Check if registration is open
- `autoAssignStudentToTerm($student)` - Auto-assign student to term

### SubjectRepository
- `getByTerm($termId)` - Get subjects by term
- `getByTeacher($teacherId)` - Get subjects by teacher
- `hasCapacity($subjectId)` - Check enrollment capacity
- `getEnrolledCount($subjectId)` - Get enrollment count

### EnrollmentRepository
- `getByStudent($studentId)` - Get student's enrollments
- `isEnrolled($studentId, $subjectId)` - Check enrollment
- `enroll($studentId, $subjectId)` - Enroll student
- `withdraw($enrollmentId)` - Withdraw enrollment
- `updateFinalGrade($enrollmentId, $grade, $letterGrade)` - Update grade

### SessionRepository
- `getBySubject($subjectId)` - Get sessions by subject
- `getLiveSessions()` - Get live Zoom sessions
- `getRecordedSessions()` - Get recorded videos
- `calculateAttendanceRate($sessionId)` - Calculate attendance %

### AttendanceRepository
- `getBySession($sessionId)` - Get attendance for session
- `markAttended($studentId, $sessionId)` - Mark attendance
- `updateWatchProgress($studentId, $sessionId, $percentage)` - Track video progress
- `calculateStudentAttendanceRate($studentId, $subjectId)` - Calculate rate

### EvaluationRepository
- `getByStudentAndSubject($studentId, $subjectId)` - Get grades
- `grade($evaluationId, $earnedScore, $feedback)` - Grade evaluation
- `calculateTotalScore($studentId, $subjectId)` - Calculate total score
- `getPendingEvaluations($subjectId)` - Get pending evaluations

## Dependency Injection

Repositories and Services are injected via constructor:

```php
class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private SubjectRepositoryInterface $subjectRepository
    ) {}
}
```

## Service Provider Registration

Register repository bindings in `app/Providers/RepositoryServiceProvider.php`:

```php
public function register(): void
{
    $this->app->bind(
        TermRepositoryInterface::class,
        TermRepository::class
    );

    $this->app->bind(
        SubjectRepositoryInterface::class,
        SubjectRepository::class
    );

    // ... more bindings
}
```

## Testing

Repositories are easily mockable in tests:

```php
public function test_student_can_enroll_in_subject()
{
    // Mock repository
    $enrollmentRepo = Mockery::mock(EnrollmentRepositoryInterface::class);
    $enrollmentRepo->shouldReceive('enroll')
        ->once()
        ->with(1, 1)
        ->andReturn(new Enrollment());

    $this->app->instance(EnrollmentRepositoryInterface::class, $enrollmentRepo);

    // Test controller action
    $response = $this->post('/api/v1/enrollments', [
        'subject_id' => 1
    ]);

    $response->assertStatus(201);
}
```

## Best Practices

1. **Keep Controllers Thin**: Controllers should only handle HTTP requests/responses
2. **Business Logic in Services**: All business logic goes in service layer
3. **Data Access in Repositories**: Only repositories interact with models
4. **Type Hints**: Always use type hints for parameters and return types
5. **Interface Programming**: Depend on interfaces, not implementations
6. **Single Responsibility**: Each method should do one thing well
7. **Query Optimization**: Use eager loading in repositories to avoid N+1 queries

## Example Flow

**Student Enrollment Flow**:

```
1. POST /api/v1/enrollments
   ↓
2. EnrollmentController@store
   ↓
3. EnrollmentService->enrollStudent()
   ├── SubjectRepository->hasCapacity()
   ├── EnrollmentRepository->isEnrolled()
   └── EnrollmentRepository->enroll()
   ↓
4. Return EnrollmentResource
```

## Migration to Repository Pattern

If converting existing code:

### Before (Direct Model Usage):
```php
// Controller
public function index()
{
    $terms = Term::with('program')->where('status', 'active')->get();
    return response()->json($terms);
}
```

### After (Repository Pattern):
```php
// Controller
public function index()
{
    $terms = $this->termRepository->getActiveTerms();
    return TermResource::collection($terms);
}

// TermRepository
public function getActiveTerms(): Collection
{
    return $this->model->with('program')
        ->where('status', 'active')
        ->get();
}
```

## Additional Resources

- [Laravel Repository Pattern](https://laravel.com/docs/repositories)
- [Clean Architecture in Laravel](https://www.youtube.com/watch?v=MrNicpqa6BY)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
