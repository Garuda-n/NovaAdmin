# Dynamic Sidebar Menu System Plan

This document outlines the implementation plan for building a database-driven dynamic sidebar menu system with a management UI in the NovaAdmin project.

## Database Layer

### 1. Migration: `create_menus_table`
Columns to create:
- `id` (primary key)
- `name` (string) - Display name of the menu
- `route` (string, nullable) - Route name (e.g., `users.index`), null for dropdown groups
- `icon` (string, nullable) - Heroicon name (e.g., `home`, `users`, `cog-6-tooth`)
- `parent_id` (foreignId, nullable, constrained to `menus`, cascade on delete) - For submenus
- `permission_slug` (string, nullable) - Permission slug required to view the menu
- `order` (integer, default 0) - For ordering/sorting items
- `status` (boolean, default true) - Active/Inactive toggle
- `timestamps`

### 2. Seeder: `MenuSeeder`
Seed all current sidebar items with their icons, routes, permissions, and groups:
- **Dashboard** (`dashboard.view`, icon: `home`)
- **Catalog Masters** (Dropdown parent, icon: `squares-2x2`)
  - Taxes (`taxes.view`, icon: `users`)
  - UOM (`uoms.view`, icon: `scale`)
  - Financial Year (`financial-years.view`, icon: `calendar-days`)
  - Counters (`counters.view`, icon: `ticket`)
- **Administration** (Dropdown parent, icon: `building-office-2`)
  - Users (`users.view`, icon: `users`)
  - Roles (`roles.view`, icon: `shield-check`)
- **Masters** (Dropdown parent, icon: `building-library`)
  - Companies (`companies.view`, icon: `building-office`)
  - Branches (`branches.view`, icon: `map-pin`)
- **Product Masters** (Dropdown parent, icon: `cube`)
  - Categories (`categories.view`, icon: `rectangle-group`)
  - Brands (`brands.view`, icon: `tag`)

---

## Models

### 1. `app/Models/Menu.php`
Define relationships and helper scopes:
- `parent()`: `belongsTo(Menu::class, 'parent_id')`
- `children()`: `hasMany(Menu::class, 'parent_id')->orderBy('order')`
- Scope `active()`: filters `status = true`
- Scope `roots()`: filters where `parent_id` is null

---

## Sidebar Integration

### 1. `resources/views/layouts/sidebar.blade.php`
Update sidebar template to query root-level menus and loop through them dynamically:
- If a menu has a `permission_slug`, wrap it in `@can($menu->permission_slug)`.
- If a menu is a parent group (has children, `route` is null), only display it if the user has permission to see at least one child menu.
- Render icons dynamically using `<x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />`.

---

## Menu Management UI (Administration)

### 1. Routes: `routes/web.php`
Add Menu CRUD resource under Administration group:
- `Route::resource('menus', MenuController::class)->middleware('permission:menus.view');` (and similar mappings for create, edit, delete).

### 2. Controller: `app/Http/Controllers/MenuController.php`
Implement CRUD operations:
- `index()`: Display hierarchical list of menus.
- `create()`/`edit()`: Allow choosing a display name, route name, icon name, parent menu, ordering index, and permission constraint.

### 3. Views: `resources/views/menus/`
Create views with consistent styling:
- `index.blade.php`: Table listing menus.
- `create.blade.php` / `edit.blade.php`: Forms to add and edit menus.
