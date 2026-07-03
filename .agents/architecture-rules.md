# Architecture Rules

Architecture Style:

Modular Monolith

Mandatory Layers:

Presentation
↓
Application
↓
Domain
↓
Infrastructure

Allowed Flow:

Controller
↓
Service
↓
Repository
↓
Model

Forbidden Flow:

Controller
↓
Model

Forbidden Flow:

Filament Resource
↓
Model

Business logic must live inside Services.

Database access must live inside Repositories.

Validation must live inside:

- FormRequest
- DTO Validation
- Domain Validation

Never place business rules inside:

- Controllers
- Filament Resources
- Blade Files
- Livewire Components

Use constructor dependency injection everywhere.

No service locator pattern.

All external integrations must use interfaces.