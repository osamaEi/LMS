# Mobile attendance alerts

`GET /api/v1/student/attendance-alerts`

Requires `Authorization: Bearer <student-token>`. No request parameters.
Uses the authenticated student's attendance assignments; another student's ID cannot be supplied.

Response: `{"success":true,"data":{"has_alerts":false,"alerts":[]}}` when no warning applies.

Each alert contains `subject_id`, `subject_name`, `code`, `severity`, Arabic `title` and `message`,
`total_sessions`, `absent_sessions`, `excused_sessions`, `absence_percent`,
`allowed_absence_percent`, `remaining_allowed_absences`, and `blocked`.

- `absence_limit_approaching` / `warning`: at least one unexcused absence and at most one further absence allowed.
- `absence_limit_exceeded` / `danger`: the student is blocked under the existing subject attendance limit.
- Equality with the allowed limit is a warning, not a block; blocking requires exceeding it.
- Approved apologies do not count against the limit. Exempt subjects and disabled limits produce no alerts.
- Calculation reuses `AttendanceLimitService`: recorded absences in completed sessions divided by all non-deleted sessions for the subject. No new program-wide or subjectless-session limit is introduced.

The mobile UI displays one banner per returned alert, using `severity` for its color.
Unauthenticated requests return `401`; non-student accounts return `403`.
