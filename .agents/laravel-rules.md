# Laravel Rules

Use:

- Form Requests
- API Resources
- Policies
- Events
- Listeners
- Jobs
- Notifications

Prefer:

- Service Classes
- DTOs

Mandatory:

- UUID primary keys
- Soft Deletes where specified
- Repository Pattern

Avoid:

- Fat Models
- Fat Controllers
- God Services

Use queue for:

- Report Generation
- Image Processing
- Notifications

Use transactions for:

- Attendance Check In
- Attendance Check Out
- Approval Workflow