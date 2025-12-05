# MercurySuite — Enterprise Blueprint (ERP + SaaS + HRM + Finance + CRM)

**MercurySuite** is a deliberate, public blueprint that demonstrates how I design, organize, and implement large-scale Laravel-based enterprise systems.
This repository intentionally contains **only the most representative files** and patterns (controllers, actions, services, repositories, interfaces, traits, request validation, jobs, CI/CD) to show architectural intent without exposing private production code.

> Purpose: show _how_ I structure and solve problems at enterprise scale so technical leads, recruiters, and clients can quickly evaluate engineering standards.

---

## Table of contents

1. Architecture Overview  
2. Why this structure (merged with development philosophy)  
3. Performance & Big Data strategy (with small realistic code samples)  
4. Security practices (with examples)  
5. CI/CD & Deployment approach (example pipeline)  
6. UI flow: realistic end-to-end user story (role-based)  
7. API versioning patterns and examples  
8. Why MercurySuite is a valuable blueprint

---

## 1) Architecture Overview

MercurySuite is designed with **domain separation**, **explicit responsibilities**, and **testability**:

```md
app/
├─ Domain/
│ ├─ Billing/
│ │ ├─ Actions/
│ │ ├─ Services/
│ │ └─ Repositories/
│ ├─ HRM/
│ └─ CRM/
├─ Actions/ # cross-domain actions & domain-level orchestration
├─ Services/ # shared services (pricing, notifications, reporting)
├─ Repositories/ # persistence abstractions (interfaces + impl)
├─ Http/
│ ├─ Controllers/
│ └─ Requests/
├─ Jobs/ # queued background tasks
├─ Models/
└─ Traits/
```

--- 

## Key patterns:

- **Thin Controllers**: orchestrate only; call Actions.
- **Action classes**: single-responsibility units encapsulating business steps.
- **Service classes**: domain-shared logic that may be reused from actions/controllers.
- **Repository + Interface**: abstract persistence for testing and future DB swaps.
- **Traits**: small cross-cutting behavior (formatting, logging helpers).
- **Jobs/Queues**: heavy or long-running tasks are queued for throughput and reliability.

---

## 2) Why this structure — development philosophy

- **Domain-first**: group code by business capability (Billing, HRM, CRM). This reduces cognitive load and makes ownership clear.
- **Single Responsibility**: classes do one thing. Easier to test and reason about.
- **Explicit dependencies via constructor injection**: avoids service locators and hidden coupling.
- **Interfaces + Repositories**: make mocking/testing easy and allow persistence replacement without changing domain logic.
- **Queue-first for heavy processes**: protects user experience and scales horizontally.
- **Readable naming**: use full, meaningful names (no `XController`, `YService`), which improves team onboarding speed.

---

## 3) Performance & Big Data Strategy

Short strategy summary:

- Use **Redis** for caching and queues. Cache hot read paths.
- Use **chunked processing** for bulk data (CSV imports, report generation).
- Break large operations into jobs, each processing a safe `chunkSize`.
- Use **cursor-based pagination** for high-traffic list endpoints.
- Avoid expensive joins; prefer carefully maintained read tables or materialized views for analytics.
- Set appropriate DB indexes and monitor slow queries.

Small realistic examples (see code files under `examples/`):

- Chunked job processing pattern: `ImportCustomersJob` processes rows in batches.
- Cache-aside pattern in `PriceCalculationService`.

(Links below lead to example files included in this repository.)

---

## 4) Security practices (short, realistic)

- Centralized request validation (Form Request classes) — prevents invalid input from reaching domain logic.
- Policy-based authorization (Laravel Policies) — per-model, per-action permission checks.
- Rate limiting for public endpoints and auth endpoints.
- Data encryption at rest for sensitive fields (e.g., use Laravel's encrypted casting).
- Audit logs for financial actions (immutable transaction logs).
- Secrets and credentials stored in environment and secret manager (never in repo).

Example: `CustomerCreateRequest` ensures fields are validated before action execution (see examples below).

---

## 5) CI/CD & Deployment approach (example)

- CI: static analysis (PHPStan), lint, composer install, run tests.
- CD: build → artifact → zero-downtime deploy (blue/green or atomic) with migration steps and health checks.
- Example pipeline (GitHub Actions) provided in `examples/.github/workflows/ci-cd.yml`.

Key details:
- Run migrations in a single transaction where possible.
- Use `php artisan queue:restart` as part of deployment.
- Health-check endpoint and rolling restarts.

---

## 6) UI Overview — realistic role-based flow (story)

**Actors**: System Admin, Marketer, Accountant, Manager, Employee.

Flow (login → complete work → logout):

1. **Login (all)** — Central auth. Roles determine UI and API scopes.
2. **Admin** — creates a Product, defines SaaS plans; sets Marketer user; configures company settings.
3. **Marketer** — logs in, sees monthly target dashboard → creates a Lead in CRM → converts Lead to Customer with chosen Plan → the system creates Invoice and records Marketer as referrer.
4. **Accountant** — views pending invoices, reconciles payments, records expenses, opens Payroll to confirm salary runs.
5. **Manager** — reviews Employee daily reports, approves/requests changes; reviews attendance anomaly reports.
6. **Employee** — marks attendance, submits daily report, views approved tasks and KPI impact.
7. **System** — nightly jobs: invoice generation, recurring subscription charge, commission allocation to marketer wallet, backup tasks.
8. **Logout** — session destroyed, audit log appended.

This story illustrates how actions/services/jobs interplay in real usage.

---

## 7) API versioning examples & architecture

- Public API endpoints follow `/api/v1/...` (stable) and `/api/v2/...` (next).
- Controllers remain thin; versioned controllers call shared Actions/Services.
- Use route groups and route model binding.

Example patterns included:
- `routes/api_v1.php`
- `routes/api_v2.php`

Versioned controllers can reuse the same Actions but evolve request/response DTOs separately.

---

## 8) Why MercurySuite is valuable as a blueprint

- Provides an exact, opinionated, production-proven structure for enterprise Laravel systems.
- Demonstrates familiarity with large codebases, testability, and operational concerns.
- Recruiters and clients can quickly evaluate code quality, architecture decisions, and production readiness from a small but representative subset of files.
- Enables conversation in interviews: discuss trade-offs and design choices without leaking proprietary client code.

---

## Example files included in this repo

- `app/Http/Controllers/Api/V1/CustomerController.php`
- `app/Actions/Customer/CreateCustomerAction.php`
- `app/Services/PriceCalculationService.php`
- `app/Repositories/Contract/CustomerRepositoryInterface.php`
- `app/Repositories/Eloquent/EloquentCustomerRepository.php`
- `app/Http/Requests/CustomerCreateRequest.php`
- `app/Traits/FormatsTimestamps.php`
- `app/Jobs/ImportCustomersJob.php`
- `database/migrations/2025_01_01_000000_create_customers_table.php`
- `.github/workflows/ci-cd.yml`
- `routes/api_v1.php`
- `routes/api_v2.php`

Each of those files is included below in the `examples/` section; copy them into your demo repo to show architecture.

---

## How to use this blueprint in interview / portfolio

1. Create a repo `mercurysuite-blueprint`.
2. Add this `README.md`.
3. Add the `examples/` files (controllers/services/repositories/requests/jobs).
4. Link to a short Loom or recorded walkthrough (optional) where you open a few files and explain choices.
5. Use this as a talking artifact in interviews: focus on trade-offs, scaling choices, and testability.

---

## Next steps I can prepare for you (optional)

- A short script / 10-minute walkthrough to present during interviews.
- A redacted sample architecture PDF derived from this blueprint.
- A GitHub Pages site summarizing this blueprint.

---

```

---

# EXAMPLES: Important files (copy & paste-ready)

Below are the important example files referenced

---

## 1) `routes/V1/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerController;

Route::prefix('v1')->group(function () {
    Route::post('customers', [CustomerController::class, 'store']);
    Route::get('customers/{customer}', [CustomerController::class, 'show']);
});
```

---

## 2) `routes/V2/api.php` (versioning example)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\CustomerController as CustomerControllerV2;

Route::prefix('v2')->group(function () {
    Route::post('customers', [CustomerControllerV2::class, 'store']);
    // v2 might use different DTOs / request format while reusing Actions
});
```

---

## 3) `app/Http/Controllers/Api/V1/CustomerController.php`

```php
<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCreateRequest;
use App\Actions\Customer\CreateCustomerAction;

class CustomerController extends Controller
{
    /**
     * Create a customer (thin controller: orchestrates only).
     */
    public function store(CustomerCreateRequest $request)
    {
        $customer = (new CreateCustomerAction())->execute($request->validated());

        return response()->json([
            'data' => $customer,
        ], 201);
    }

    public function show($customerId)
    {
        // Example: use repository pattern for retrieval
        $repository = app(\App\Repositories\Contract\CustomerRepositoryInterface::class);
        $customer = $repository->findById((int) $customerId);

        if (! $customer) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $customer]);
    }
}
```

---

## 4) `app/Actions/Customer/CreateCustomerAction.php`

```php
<?php
namespace App\Actions\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CreateCustomerAction
{
    /**
     * Execute the creation steps for a new customer.
     * Single responsibility: create customer and trigger post-create events.
     */
    public function execute(array $payload): Customer
    {
        return DB::transaction(function () use ($payload) {
            $customer = Customer::create([
                'name' => $payload['name'],
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
            ]);

            // Keep domain events and other side effects as Jobs
            if (! empty($payload['imported_by'])) {
                // e.g. dispatch job to notify or log import
            }

            return $customer;
        });
    }
}
```

---

## 5) `app/Services/PriceCalculationService.php`

```php
<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PriceCalculationService
{
    /**
     * Return effective price for a product and customer.
     * Cache result for short TTL to reduce DB pressure on price lookups.
     */
    public function getEffectivePrice(int $productId, int $customerId): float
    {
        $cacheKey = "pricing:product:$productId:customer:$customerId";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($productId, $customerId) {
            return $this->calculatePrice($productId, $customerId);
        });
    }

    protected function calculatePrice(int $productId, int $customerId): float
    {
        // Simplified: in real world apply discounts, tiers, overrides
        return 99.0;
    }
}
```

---

## 6) Repository Interface: `app/Repositories/Contract/CustomerRepositoryInterface.php`

```php
<?php
namespace App\Repositories\Contract;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;
    public function create(array $data): Customer;
    public function update(int $id, array $data): ?Customer;
}
```

---

## 7) Repository Implementation: `app/Repositories/Eloquent/EloquentCustomerRepository.php`

```php
<?php
namespace App\Repositories\Eloquent;

use App\Repositories\Contract\CustomerRepositoryInterface;
use App\Models\Customer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::query()->find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): ?Customer
    {
        $customer = $this->findById($id);
        if (! $customer) {
            return null;
        }

        $customer->update($data);
        return $customer;
    }
}
```

**Bind the interface to implementation in a service provider:**

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contract\CustomerRepositoryInterface;
use App\Repositories\Eloquent\EloquentCustomerRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(
            CustomerRepositoryInterface::class,
            EloquentCustomerRepository::class
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        //
    }
}
```

---

## 8) Request validation: `app/Http/Requests/CustomerCreateRequest.php`

```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Leave as true for demo; in real world implement policy checks.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:30',
        ];
    }
}
```

---

## 9) Trait: `app/Traits/FormatsTimestamps.php`

```php
<?php
namespace App\Traits;

trait FormatsTimestamps
{
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }
}
```

---

## 10) Job: `app/Jobs/ImportCustomersJob.php` (chunked processing pattern)

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contract\CustomerRepositoryInterface;

class ImportCustomersJob implements ShouldQueue
{
    use Queueable;

    protected array $rows;
    protected int $chunkNumber;

    public function __construct(array $rows, int $chunkNumber = 1)
    {
        $this->rows = $rows;
        $this->chunkNumber = $chunkNumber;
    }

    public function handle(CustomerRepositoryInterface $customerRepository)
    {
        foreach ($this->rows as $row) {
            try {
                $customerRepository->create([
                    'name'  => $row['name'],
                    'email' => $row['email'] ?? null,
                    'phone' => $row['phone'] ?? null,
                ]);
            } catch (\Throwable $e) {
                Log::error('ImportCustomersJob.failed', ['error' => $e->getMessage(), 'row' => $row]);
            }
        }

        // Optional: dispatch next chunk or mark progress
    }
}
```

---

## 11) Migration snippet: `database/migrations/2025_01_01_000000_create_customers_table.php`

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->timestamps();

            // Example index: frequently queried by email/phone
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
```

---

## 12) Model skeleton: `app/Models/Customer.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsTimestamps;

class Customer extends Model
{
    use FormatsTimestamps;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
```

---

## 13) API Versioned Controller (v2 example): `app/Http/Controllers/Api/V2/CustomerController.php`

```php
<?php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCreateRequest;
use App\Actions\Customer\CreateCustomerAction;
use App\Services\PriceCalculationService;

class CustomerController extends Controller
{
    protected PriceCalculationService $priceCalculationService;

    public function __construct(PriceCalculationService $priceCalculationService)
    {
        $this->priceCalculationService = $priceCalculationService;
    }

    public function store(CustomerCreateRequest $request)
    {
        $payload = $request->validated();

        // v2 uses enriched payload (example)
        $payload['external_reference'] = $request->header('X-External-Ref');

        $customer = (new CreateCustomerAction())->execute($payload);

        // Example: attach calculated default price for demo purposes
        $customer->default_price = $this->priceCalculationService->getEffectivePrice(1, $customer->id);

        return response()->json(['data' => $customer], 201);
    }
}
```

---

## 14) CI/CD pipeline (GitHub Actions): `examples/.github/workflows/ci-cd.yml`

```yaml
name: CI

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install --no-progress --no-suggest --prefer-dist
      - name: Run linters
        run: composer run lint || true
      - name: Run tests
        run: vendor/bin/phpunit --colors=never --testsuite=unit
```

Notes: adapt to your exact CI/CD process; this is a minimal pipeline demonstrating CI steps.

---

# Short usage notes & talking points for interviews

* **Why Actions**: isolate business steps (e.g., `CreateCustomerAction` has transactional logic) so you can test the domain without HTTP layer.
* **Why Repositories**: decouple domain from persistence; easier to mock in tests.
* **Why Services**: stateful rules across modules (pricing, reporting) belong to services to avoid duplication.
* **Why Jobs**: long-running, high-latency tasks (imports, recurring billing) handled asynchronously for reliability and throughput.
* **Why Requests**: keep validation out of controller & domain for clarity and single source of truth.

---

# Final notes

* This blueprint is intentionally minimal but demonstrates the exact patterns I use in production systems.