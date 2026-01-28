Project Information
===================

Overview
--------

4Byte.dev is an open-source community platform designed for developers. It offers features such as articles, courses, news, and interactive code spaces (CodeSpace).

The project is built on the **Laravel Modules** structure for scalability and maintainability, utilizing a modern **React + Inertia.js** frontend.

Key Features
------------

* **Modular Architecture:** Decoupled modules for Article, Course, News, Entry, User, Tag, Category, etc.
* **CodeSpace:** Browser-based code editor and workspace integrated with Monaco Editor.
* **Recommendation System:** Content recommendations based on user interactions using **Gorse**.
* **Admin Panel:** Powerful content and user management via **Filament**.
* **Powerful Search:** Fast, typo-tolerant search powered by **Meilisearch**.
* **Social Interaction:** Likes, comments, follows, and save features.
* **Roles & Permissions:** Detailed authorization using Spatie Permission and Filament Shield.

Technology Stack
----------------

Backend & Infrastructure
~~~~~~~~~~~~~~~~~~~~~~~~

* **Framework:** Laravel 11/12
* **Database:** PostgreSQL (App), MySQL (Gorse data)
* **Cache & Queue:** Redis
* **Search Engine:** Meilisearch
* **Recommendation Engine:** Gorse
* **Admin Panel:** FilamentPHP
* **Containerization:** Docker & Docker Compose

Frontend
~~~~~~~~

* **Framework:** React
* **Adapter:** Inertia.js
* **Styling:** TailwindCSS & Shadcn UI (Radix)
* **Package Manager:** Bun / NPM
* **Build Tool:** Vite
