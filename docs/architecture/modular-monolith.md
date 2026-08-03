# Modular Monolith

This application is configured as a modular monolith: one deployable Laravel app with code organized by business capability.

## Module Layout

Modules live under `modules/{ModuleName}` and use the `Modules\\` namespace.

Suggested structure for each module:

```text
modules/Inventory
├── Actions
├── Data
├── Database
├── Domain
├── Http
│   ├── Controllers
│   └── Livewire
├── Models
├── Providers
├── routes
│   └── web.php
└── resources
    └── views
```

## Registration

Add a module service provider to `config/modules.php`, then add the module key to `enabled`.

Module providers should extend `App\Support\Modules\ModuleServiceProvider`. Routes, migrations, views, and translations are loaded from conventional module paths when present.

## Boundaries

Keep database tables, Livewire components, controllers, policies, actions, and domain services inside their owning module. Shared cross-module primitives can live in `app/Support`, but business behavior should stay inside modules.
