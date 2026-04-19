# Submission — CLT Toolbox Feature Test

Candidate: Alfin Ellsyan
Branch: `alfin-assignment`

## Live Demo

Railway deployment (auto-deploys on push to this branch):

**<https://candidate-test-production-e511.up.railway.app>**

Visiting the root URL redirects to the login page; every UI route
requires an authenticated session.

## Test Accounts

Seeded automatically on every deploy via a dedicated migration
(`database/migrations/2026_04_19_000001_promote_test_user_to_admin.php`),
so roles stay deterministic across redeploys.

| Email                 | Password   | Role   | What they can do                                  |
| --------------------- | ---------- | ------ | ------------------------------------------------- |
| `test@example.com`    | `password` | Admin  | Full access, including user management at `/users` |
| `staff@example.com`   | `password` | Staff  | Create / edit / delete suppliers, layups, layers  |
| `viewer@example.com`  | `password` | Viewer | Read-only — listings, detail, export              |

Public registration is disabled; only an admin can provision users
from the Users page.

## Features Implemented

### Core (spec checklist)
- [x] CRUD Suppliers, Layups, Layers with the Supplier → Layup → Layer
      hierarchy enforced via route model binding.
- [x] Export-by-Supplier: `GET /suppliers/{id}/download` returns a
      JSON snapshot with the supplier plus every layup and layer,
      with a `Content-Disposition: attachment` filename.
- [x] Import-by-Supplier: `POST /api/suppliers/{id}/import` accepts
      CSV or JSON, creates/updates layups and layers.

### Conflict resolution
- [x] All four spec strategies: `skip`, `overwrite`, `duplicate`,
      `reject`, plus the bonus `manual` strategy.
- [x] Side-by-side conflict UI on the supplier detail page:
      Existing vs Importing columns with differing fields highlighted,
      per-row Keep Existing / Accept Incoming radios, and
      bulk Apply-All actions. See
      `resources/views/manager/suppliers/show.blade.php`.
- [x] Dry-run checkbox returns conflicts without writing.

### Architecture (bonus)
- Repository pattern: `SupplierRepository`, `LayupRepository`,
  `LayerRepository` bound via `AppServiceProvider::$bindings`.
- Service pattern: `SupplierExportService`, `SupplierImportService`
  bound to interfaces.
- Policies: `SupplierPolicy`, `LayupPolicy`, `LayerPolicy`.
- FormRequest validation on every write endpoint
  (`SupplierImportRequest`, etc.).
- Middleware: `EnsureAdmin`, `EnsureStaff` gate the role-sensitive
  routes.

### UX polish
- Manager layout with navbar, dashboard, and role badge.
- Flash toast partial, loading states on form submit, illustrated
  empty states, breadcrumb partial, custom delete-confirm modal.
- Mobile hamburger drawer via Alpine teleport.
- Profile page re-themed into the manager shell.

### Security
- Public registration removed.
- Login throttled (Breeze default, 5 attempts per throttle key).
- Strong password rule (`min:8`, mixed case, numbers).
- API routes require `web + auth`; mutations require the `staff`
  role; user management requires `admin`.
- Guard blocks demoting / deleting the last remaining admin.

## Local Setup

See [SETUP.md](./SETUP.md) for the full setup guide and API reference.

Short version:
```bash
cp .env.example .env
composer install
npm ci
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Repo Layout Notes

- Branch `alfin-assignment` is the submission branch.
- `nixpacks.toml` drives the Railway build and start command; on
  every boot it runs `php artisan migrate --force && php artisan
  db:seed --force`, both of which are idempotent.
- Commits are conventional-commit style and describe the *why*.
