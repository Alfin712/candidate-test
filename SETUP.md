# Setup & Run Guide

> For submission overview + live demo URL + credentials, see
> [SUBMISSION.md](./SUBMISSION.md).

## Requirements
- PHP 8.2+
- MySQL 8.x (running on localhost:3306)
- Composer
- Node.js 20+ (for the Vite asset build)

## Steps

### 1. Copy .env
```bash
cp .env.example .env
```

### 2. Install dependencies
```bash
composer install
npm ci
```

### 3. Generate app key
```bash
php artisan key:generate
```

### 4. Run migrations
```bash
php artisan migrate
```

### 5. Seed sample data
```bash
php artisan db:seed
```

This creates three accounts — `test@example.com` (admin),
`staff@example.com` (staff), `viewer@example.com` (viewer) — all
with password `password`. Public registration is disabled; provision
additional accounts from the Users page as an admin.

### 6. Build frontend assets
```bash
npm run build   # production
# or: npm run dev    # hot reload during development
```

### 7. Run tests
```bash
php artisan test
# or
php artisan test --filter=SupplierTransfer
```

## API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | /api/suppliers | List all suppliers (paginated) |
| POST | /api/suppliers | Create supplier |
| GET | /api/suppliers/{id} | Show supplier with layups+layers |
| PUT | /api/suppliers/{id} | Update supplier |
| DELETE | /api/suppliers/{id} | Delete supplier |
| POST | /api/suppliers/{id}/layups | Add layup |
| GET | /api/suppliers/{id}/layups/{lid} | Show layup with layers |
| PUT | /api/suppliers/{id}/layups/{lid} | Update layup |
| DELETE | /api/suppliers/{id}/layups/{lid} | Delete layup |
| POST | /api/suppliers/{id}/layups/{lid}/layers | Add layer |
| PUT | /api/suppliers/{id}/layups/{lid}/layers/{lrid} | Update layer |
| DELETE | /api/suppliers/{id}/layups/{lid}/layers/{lrid} | Delete layer |
| GET | /api/suppliers/{id}/export | Export supplier as JSON |
| POST | /api/suppliers/{id}/import | Import with conflict resolution |

## Architecture

- **Repository pattern:** `SupplierRepository`, `LayupRepository`, `LayerRepository` (contracts under `App\Contracts`, bound via `AppServiceProvider::$bindings`)
- **Service pattern:** `SupplierExportService`, `SupplierImportService` (contracts bound)
- **Policies:** `SupplierPolicy`, `LayupPolicy`, `LayerPolicy` — registered via `Gate::policy()` in `AppServiceProvider::boot()`
- **FormRequest validation** on every write endpoint (`authorize()` delegates to policies when user authenticated)
- **Route Model Binding** on all nested routes

## Conflict Strategies Supported

- `skip` — keep existing, drop incoming on conflict
- `overwrite` — incoming wins
- `reject` — abort entire import if any conflict, return 409
- `duplicate` — create new layup with `(imported)` suffix when conflicting
- `manual` — per-conflict resolution via `resolutions[]`

## Import Payload Format

```json
{
  "strategy": "skip",
  "dry_run": true,
  "payload": {
    "name": "Updated Supplier Name",
    "layups": [
      {
        "name": "LU-001",
        "description": "Quasi-isotropic layup",
        "status": "Active",
        "layers": [
          { "layer_order": 1, "thickness": 0.25, "width": 100.0, "angle": 0 },
          { "layer_order": 2, "thickness": 0.25, "width": 100.0, "angle": 45 }
        ]
      }
    ]
  }
}
```

## Conflict Resolution

When `strategy` is `manual`, pass `resolutions` array:

```json
{
  "strategy": "manual",
  "resolutions": [
    {
      "layup_name": "LU-001",
      "layer_order": 2,
      "action": "accept_incoming"
    }
  ],
  "payload": { ... }
}
```

Actions: `keep_existing` | `accept_incoming`

## Response Format

All endpoints return:
```json
{
  "status": "success | error | conflict",
  "message": "...",
  "data": { ... }
}
```

Import with conflicts (409):
```json
{
  "status": "conflict",
  "message": "Conflicts detected — review and resolve",
  "data": {
    "status": "conflict",
    "dry_run": false,
    "strategy": "reject",
    "conflicts": [
      {
        "layup_name": "LU-001",
        "layer_order": 2,
        "existing": { "thickness": 0.25, "width": 100.0, "angle": 45 },
        "incoming": { "thickness": 0.30, "width": 100.0, "angle": 45 }
      }
    ]
  }
}
```
