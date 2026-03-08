# Modular Structure (app/Modules)

This project uses a modular structure to group functionality. Each module should contain:

- **Models/**: Eloquent models for this specific module.
- **Actions/**: Business logic classes (e.g. `CreateUserAction`, `AssignCourseAction`).
- **Livewire/**: Livewire components for this module's UI.
- **Policies/**: Authorization policies.
- **Requests/**: Form Request classes for validation.

## Conventions
1. Keep modules as independent as possible.
2. Use Action classes for complex logic to keep models and controllers/components thin.
3. Register routes in `routes/web.php` pointing to Livewire components in this structure.
