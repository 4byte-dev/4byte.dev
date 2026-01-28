Getting Started
===============

Prerequisites
-------------

* Docker and Docker Compose
* Make (Optional, but recommended)

Installation Guide
------------------

1. Clone the Repository
~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

   git clone https://github.com/4byte-dev/4byte.dev.git
   cd 4byte.dev

2. Configure Environment
~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

   cp .env.example .env

3. Start Docker Containers
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

   docker compose --env-file .env --profile app --profile production up -d

.. note::
   Check .env settings and Makefile profiles for development options.

4. Application Setup
~~~~~~~~~~~~~~~~~~~~

Run the following existing Make commands to initialize the database and data:

.. code-block:: bash

   make migrate
   make create-permissions
   make seed

*If make is not available, you can manually run docker exec commands for php artisan migrate and php artisan db:seed.*

5. Access the Application
~~~~~~~~~~~~~~~~~~~~~~~~~

* **Web App:** http://localhost:8000
* **Admin Panel:** http://localhost:8000/admin

Default Admin Credentials
-------------------------

* **Email:** admin@example.com
* **Password:** password

Development Commands
--------------------

Commonly used `make` commands:

* `make up`: Start containers.
* `make down`: Stop containers.
* `make logs`: View logs.
* `make shell:app`: Access the application container shell.
* `make test`: Run tests.

Contribution
------------

Please refer to the `README.md` for detailed contribution guidelines, including Pull Request processes and Commit Message standards.
