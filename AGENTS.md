# FAMS Project

Read first:

@docs/01-PRD.md
@docs/02-SAD.md
@docs/03-DDD.md
@docs/04-TBS.md
@docs/05-API-SPEC.md
@docs/06-SECURITY.md
@docs/07-TESTING.md
@docs/08-DEPLOYMENT.md

Project Rules:

@.agents/master-context.md
@.agents/coding-standards.md
@.agents/architecture-rules.md
@.agents/filament-rules.md
@.agents/testing-rules.md
@.agents/security-rules.md
@.agents/laravel-rules.md
@.agents/database-rules.md
@.agents/task-rules.md

Tech Stack:

- Laravel 13
- PHP 8.4
- Filament 5
- Livewire 3
- TailwindCSS 4
- AlpineJS
- PostgreSQL 17 + PostGIS (production)
- MariaDB/MySQL (development)
- Redis (production)
- Database queue (dev)
- MinIO / AWS S3
- dompdf, OpenSpout

Architecture:

- Modular Monolith
- DDD (Domain Driven Design)
- Repository Pattern
- Service Layer Pattern
- DTO Pattern
- Domain Exceptions

Mandatory Rules:

- No business logic in controllers
- No database queries in Filament Resources
- Use DTOs
- Use Form Requests
- Use Policies
- Use Services
- Use Repositories
- Use Domain Exceptions
- Create tests for all features
