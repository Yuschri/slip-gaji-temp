# Agents Documentation

This document outlines the agents (modules/components) used in the Laravel-based project **slip-gaji-temp**. The project follows a typical Laravel structure with separate concerns for Controllers, Models, Views, Repositories, and Services.

---

## 📁 Project Structure Overview

```
slip-gaji-temp/
├── app/
│   ├── Http/
│   │   └── Controllers/        # HTTP controllers handling HTTP requests
│   ├── Models/                 # Eloquent models representing database tables
│   ├── Repositories/           # Data access layer, abstracts Eloquent queries
│   └── Services/               # Business logic layer, orchestrates repositories and other services
├── resources/
│   └── views/                  # Blade templates for rendering HTML
├── routes/
│   └── web.php                 # Route definitions pointing to controllers
├── database/
│   ├── migrations/             # Database schema migrations
│   └── seeders/                # Database seeders
└── ... (config, public, etc.)
```

---

## 👤 Agents (Modules) Overview

| Agent (Module) | Responsibility                                                                 | Typical Location                                      |
|----------------|------------------------------------------------------------------------------|-------------------------------------------------------|
| **Controller** | Handles incoming HTTP requests, validates input, calls services, returns responses (views, JSON, redirects). | `app/Http/Controllers/` |
| **Model**      | Represents a database table, defines relationships, mutators, accessors, scopes. | `app/Models/` |
| **Repository** | Abstracts data access (Eloquent queries). Provides methods like `find`, `create`, `update`, `delete`, and custom queries. | `app/Repositories/` |
| **Service**    | Contains business logic. Orchestrates multiple repositories, performs validation, processes data, and returns results to controllers. | `app/Services/` |
| **View**       | Blade templates that present data to the user. Receives data from controllers. | `resources/views/` |
| **Route**      | Defines URI patterns and maps them to controller methods.                    | `routes/web.php` (and `api.php` if applicable) |
| **Middleware** (optional) | Filters HTTP requests entering the application (authentication, logging, etc.). | `app/Http/Middleware/` |
| **Seeder**     | Populates the database with sample or default data.                          | `database/seeders/` |
| **Migration**  | Defines database schema changes (create tables, columns, indexes).          | `database/migrations/` |

---

## 📦 How Agents Interact

1. **Route** receives an HTTP request and directs it to a **Controller** method.
2. **Controller** validates the request (via Form Request or manual validation) and delegates business logic to a **Service**.
3. **Service** orchestrates one or more **Repositories** to retrieve/persist data, applies domain rules, and returns results.
4. **Repository** interacts directly with the **Model** (Eloquent) to perform CRUD operations.
5. **Model** encapsulates database table logic, relationships, and scopes.
6. Controller receives the result from Service and returns a **View** (or JSON response) to the client.
7. **View** renders the data using Blade templating engine.

---

## 📌 Naming Conventions

| Artifact       | Naming Convention                              | Example                     |
|----------------|-----------------------------------------------|-----------------------------|
| Controller     | StudlyCase + `Controller`                     | `KaryawanController`        |
| Model          | StudlyCase (singular)                         | `Karyawan`                  |
| Repository     | StudlyCase + `Repository`                     | `KaryawanRepository`        |
| Service        | StudlyCase + `Service`                        | `KaryawanService`           |
| View           | kebab-case or snake_case Blade files          | `karyawan/index.blade.php`  |
| Route          | snake_case route names                        | `karyawan.index`            |
| Migration      | timestamp_create_table_name                   | `2026_04_01_091508_tb_karyawan.php` |
| Seeder         | Descriptive + `Seeder`                        | `DivisiJabatanSeeder`       |

---

## 🛠️ Development Guidelines

- **Controllers** should be thin: only handle HTTP concerns (input/output).  
- **Services** encapsulate use‑specific business logic; keep them testable.  
- **Repositories** return Eloquent models or collections; avoid returning raw arrays unless necessary.  
- **Models** define relationships (`belongsTo`, `hasMany`, etc.) and scopes (`scopeActive`, etc.).  
- **Views** should receive data via compact or `with`; avoid heavy logic in Blade—use View Comporters or Service Injectors if needed.  
- Keep **migrations** focused on schema changes; seeders for test/demo data.  
- Register **Repositories** and **Services** in Laravel’s Service Container (via `AppServiceProvider` or dedicated providers) for dependency injection.

---

## 📚 Example Flow: Creating a New Karyawan

1. **Route** (`routes/web.php`): `POST /karyawan` → `KaryawanController@store`
2. **Controller** (`app/Http/Controllers/KaryawanController.php`):
   ```php
   public function Store(Request $request, KaryawanService $service)
   {
       $validated = $request->validate([/* rules */]);
       $karyawan = $service->create($validated);
       return redirect()->route('karyawan.index')->with('success', 'Karyawan added');
   }
   ```
3. **Service** (`app/Services/KaryawanService.php`):
   ```php
   public function create(array $data)
   {
       // optional: additional business rules
       return $this->repository->create($data);
   }
   ```
4. **Repository** (`app/Repositories/KaryawanRepository.php`):
   ```php
   public function create(array $data)
   {
       return Karyawan::create($data);
   }
   ```
5. **Model** (`app/Models/Karyawan.php`):
   ```php
   class Karyawan extends Model
   {
       protected $fillable = ['nama', 'nip', 'divisi_id', 'jabatan_id'];
       public function divisi() { return $this->belongsTo(Divisi::class); }
   }
   ```
6. **View** (`resources/views/karyawan/create.blade.php`): Form that posts to `/karyawan`.

---

## 📖 License & Notes

This document is part of the project's internal documentation. Keep it updated whenever the folder structure or conventions change.

--- 