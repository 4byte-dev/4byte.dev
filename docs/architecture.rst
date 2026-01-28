Project Architecture
=================================

Overview
--------

This project describes a **Modular Monolith** application built with Laravel.
It leverages a module-based structure to organize code by domain (e.g., Article,
User, React) while maintaining a single deployable unit. The application is
**Event-Driven**, using Events and Listeners to decouple modules and handle side
effects.

Architectural Pattern: Modular Monolith
---------------------------------------

Unlike a traditional Monolith (where code is separated by technical layers like
Controllers/Models) or a Microservices architecture (distributed services), this
project uses a **Modular Monolith** approach.

- **Modules Directory**: All domain logic resides in ``modules/``.
- **Isolation**: Each module is a self-contained unit.
- **Dependency Management**: Modules have their own ``composer.json`` files, which
  are merged into the main project's dependencies using
  ``wikimedia/composer-merge-plugin``.
- **Module Management**: Uses ``nwidart/laravel-modules`` for loading and managing modules.

Module Directory Structure
~~~~~~~~~~~~~~~~~~~~~~~~~~

Each module in ``modules/`` typically follows this structure:

::

    modules/ModuleName/
    ├── app/                # Core logic: Controllers, Models, Events, Services
    ├── config/             # Module-specific configuration
    ├── database/           # Migrations, Factories, Seeders
    ├── lang/               # Translations
    ├── resources/          # Views, JS/Vue/React assets
    ├── routes/             # Web and API routes
    ├── tests/              # Module-specific Feature/Unit tests
    ├── composer.json       # Backend dependencies
    ├── module.json         # Module manifest
    ├── package.json        # Frontend dependencies
    └── vite.config.js      # Build configuration

Available Modules
~~~~~~~~~~~~~~~~~

The application is composed of the following modules:

- **Admin**
- **Article**
- **Category**
- **CodeSpace**
- **Course**
- **Entry**
- **News**
- **Page**
- **React**
- **Recommend**
- **Search**
- **Tag**
- **User**

Event-Driven Architecture (EDA)
-------------------------------

The application relies heavily on Events and Listeners to maintain decoupling
between modules. Instead of one module directly calling another's service class,
it dispatches an event.

Core Concepts & Benefits
~~~~~~~~~~~~~~~~~~~~~~~~

The primary goal of this architecture is to ensure high cohesion within modules
and low coupling between them.

1.  **Decoupling**: Modules can evolve (or be replaced) independently. For example,
    adding a new reaction type (like "Love") in the **React** module does not require
    changes in the **Article** module.

2.  **Scalability**: Heavy processing tasks (e.g., syncing data to the Gorse
    recommendation engine) are handled by listeners that can be easily moved to
    background queues. This keeps user requests fast and responsive.

3.  **Maintainability**: Code related to a specific feature is found in one place,
    not scattered across ``app/Http/Controllers``, ``app/Models``, etc.

Technology Stack
----------------

- **Framework**: Laravel 11/12 (PHP 8.2+)
- **Frontend**: Inertia.js (v1/v2) with React.
- **Admin Panel**: Filament v3.
- **Database**: PostgreSQL / MySQL.
- **Search**: Meilisearch / Scout.
- **Recommendation**: Gorse.
- **Media**: Spatie Media Library.
