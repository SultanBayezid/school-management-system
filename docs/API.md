# REST API — School Management System

Base URL: `/api/v1`
All responses are JSON. Authentication uses Laravel Sanctum bearer tokens.

## Authentication

### `POST /api/v1/auth/login`

Obtain an API token using application credentials.

```json
{
    "email": "admin@school.test",
    "password": "password"
}
```

**200 OK**

```json
{
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": {
        "id": 1,
        "name": "System Administrator",
        "email": "admin@school.test",
        "role": "admin"
    }
}
```

**422 Unprocessable Entity** — invalid credentials.

| Error | Meaning |
|---|---|
| `email` | Credentials incorrect or account deactivated |

Subsequent requests must send: `Authorization: Bearer <token>`

- `GET /api/v1/auth/me` — current authenticated user
- `POST /api/v1/auth/logout` — revoke the current token

## Endpoints

### Students

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/students` | Paginated list | admin, teacher |
| GET | `/students/{id}` | Single student | admin, teacher, self (student), parent of |

Query parameters for the list: `search` (name, admission no, guardian),
`class_id`, `per_page` (max 100).

```json
{
    "data": [
        {
            "id": 1,
            "admission_no": "ADM-2026-0001",
            "name": "Tanvir Hossain",
            "first_name": "Tanvir",
            "last_name": "Hossain",
            "gender": "male",
            "date_of_birth": "2019-03-14",
            "class": { "class": "Class One", "section": "Section A", "roll_no": 1 },
            "is_active": true
        }
    ],
    "links": { "...": "pagination links" },
    "meta": { "current_page": 1, "last_page": 6, "total": 120 }
}
```

### Teachers

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/teachers` | Paginated list (`search`, `per_page`) | any authenticated |
| GET | `/teachers/{id}` | Single teacher | any authenticated |

### Classes

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/classes` | All classes with sections + subjects | any authenticated |
| GET | `/classes/{id}` | Single class detail | any authenticated |

### Attendance

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/attendance` | Filter records | any (scoped for student/parent) |
| POST | `/attendance` | Bulk upsert one day | admin, teacher |

GET filters: `student_id`, `from`, `to` (dates `Y-m-d`), `per_page`.
Students automatically see only their own records; parents only their children's.

POST body:

```json
{
    "school_class_id": 1,
    "section_id": 1,
    "attendance_date": "2026-11-10",
    "statuses": {
        "1": "present",
        "2": "absent",
        "3": "late"
    }
}
```

Valid statuses: `present`, `absent`, `late`, `leave`.
Saving an already-marked day **updates** the existing records (upsert).
**201 Created** `{"saved": 3}`.

### Exams & results

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/exams` | Exams (published only for non-admins) | any authenticated |
| GET | `/exams/{id}` | Exam detail | any authenticated |
| GET | `/exams/{id}/report-card/{student_id}` | Full computed report card | any authenticated* |

\* Only for published exams (admins may view drafts).

Report card response:

```json
{
    "exam": { "id": 1, "name": "First Terminal Examination", "class": "Class One" },
    "student": { "id": 1, "name": "Tanvir Hossain", "admission_no": "ADM-2026-0001" },
    "subjects": [
        { "subject": "Mathematics", "obtained": 87, "max": 100, "percentage": 87, "grade": "A+", "point": 5 }
    ],
    "totals": {
        "obtained": 87,
        "max": 100,
        "percentage": 87,
        "average": 87,
        "grade": "A+",
        "grade_point": 5,
        "failed_subjects": 0
    },
    "class_position": 3
}
```

### Invoices

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/invoices` | Filter (`status`, `student_id`, `per_page`) | any (scoped) |
| GET | `/invoices/{id}` | Invoice + payments | admin, teacher, self, parent |

Students see only their own invoices; parents only their children's.
Response includes computed `net_amount`, `paid_amount`, `due_amount`.

### Notices

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/notices` | Active notices for caller's audience (`search`, `per_page`) | any authenticated |

### Library

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/library/books` | Catalogue (`search`, `per_page`) | any authenticated |

### Dashboard

| Method | Endpoint | Description | Access |
|---|---|---|---|
| GET | `/stats` | Live application statistics | any authenticated |

```json
{
    "students": 120,
    "teachers": 8,
    "classes": 5,
    "present_today": 104,
    "absent_today": 16,
    "unpaid_invoices": 33,
    "books_issued": 6,
    "active_notices": 4
}
```

## Error format

Validation errors return **422** with Laravel's standard structure:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "attendance_date": ["The attendance date field is required."]
    }
}
```

Unauthenticated requests return **401**; insufficient permissions return **403**.

## Rate limits

Login attempts are rate limited (5 attempts per minute per email+IP). Exceeding
the limit returns **429**.

## Token security

- Tokens are revocable via `POST /api/v1/auth/logout` (revokes current token).
- Store tokens securely; they grant the full permissions of the owning user.
- Prefer issuing dedicated low-privilege accounts (e.g. `teacher`) for integrations.
