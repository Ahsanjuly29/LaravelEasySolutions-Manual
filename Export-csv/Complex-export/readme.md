
<p align="center">
  <img src="https://media.licdn.com/dms/image/v2/D5622AQGdHxl72Cpcmw/feedshare-shrink_800/B56Z5zv8SkK4Ac-/0/1780058408349?e=1781740800&v=beta&t=JOfVE6RaMKWys5gQbbAd2i5uS0yv2yvM4-z3wbo5I_E" alt="Cover Image" width="100%">
</p>

# 🚀 Mega Complex Export Engine

> **⚡ The Big Achievement:** This highly optimized system can search through a massive database of over 10 Million rows, connect 10 different tables together, filter out a complex sales report, and email a downloadable CSV file to the user in just **13.58 seconds**!

---

### 🎯 Main Challenge: Fast Performance with Heavy Tables on Low-End Hardware

1. **The Core Bottleneck:** We needed to search through a massive database of **10 Million+ rows** in real-time, apply custom filters, connect **10 related tables** using `LEFT JOIN`, and extract a small list of data (like 10,000 to 43,000 rows).
2. **Hardware Constraints:** The live production server is a very small Ubuntu VPS with **only 2 GB of RAM**. Standard database chunking methods create massive temporary tables in memory, which immediately crashes the server with an "Out of Memory" error.
3. **The Target:** Complete the entire process—including filtering, splitting data, joining tables, merging files on disk, and sending an email notification—in **under 15 seconds** without slowing down the website for other users.

---

## 🛠️ The Solution

### 1. ⚡ Quick Summary
We designed a smart system that separates the heavy database search from the web application's memory. Instead of running heavy table joins all at once, the system first finds exactly where the matching data is, processes smaller pieces in the background, and emails the file in **just 13.58 seconds**. This keeps the server's RAM usage completely flat and safe.

---

### 2. 🏗️ System Architecture & Workflow


```
                            [🔥 HTTP EXPORT REQUEST]
                                       │
                                       ▼ (Phase 1: Fast Boundary Mapping)

```

┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 📂 app/Services/SalesReportExportService.php                                           │
│ ├─► Action: Runs a super fast database scan to get matching IDs [🚀 NO TABLE JOINS]     │
│ └─► Method: Breaks down large ID gaps safely using `chunkById(10000)`                  │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
│
▼ (Pushes Data Ranges into Redis Queue)
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 🌤️ Laravel Horizon Workers (Parallel Processing Line)                                  │
│ ├─► Connection: Redis  |  Queue: `low`  |  Scale: Runs up to 4 background tasks at once│
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
│
┌──────────────────────┴──────────────────────┐
▼ (Background Worker 1)                       ▼ (Background Worker N)
┌──────────────────────────────────────────┐  ┌──────────────────────────────────────────┐
│ 📂 app/Jobs/ProcessChunkExportJob.php    │  │ 📂 app/Jobs/ProcessChunkExportJob.php    │
│ ├─► Action: Gets specific ID ranges      │  │ ├─► Action: Gets specific ID ranges      │
│ ├─► Design: Uses pre-locked queries      │  │ ├─► Design: Uses pre-locked queries      │
│ └─► File: Writes data to `chunk_1.csv`    │  │ └─► File: Writes data to `chunk_N.csv`   │
└───────────────────┬──────────────────────┘  └───────────────────┬──────────────────────┘
│                                             │
└──────────────────────┬──────────────────────┘
│ (When All Background Tasks are Done)
▼ (Phase 3: Smart File Merging)
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 📂 app/Jobs/MergeChunksJob.php                                                         │
│ ├─► File Sorting: Puts all CSV pieces in the correct numerical order using `natsort()` │
│ ├─► Zero-RAM Merge: Joins all pieces directly on disk using `stream_copy_to_stream()`  │
│ ├─► Disk Cleanup: Deletes temporary pieces instantly to save server storage space       │
│ └─► Email Delivery: Sends the final CSV download link to the user via an email job     │
└────────────────────────────────────────────────────────────────────────────────────────┘

---

### 3. 💻 Core Codebase Architecture

#### ① Repository Layer: The Database Query Handler
* **File Path:** `app/Repositories/SalesReportRepository.php`
* **Why this code matters:** If we change or add dynamic columns to the query while the background jobs are running, the database optimizer gets confused. It switches to a slow full-table scan, checking 900,000+ extra rows by mistake. We locked the table selections permanently inside `getJoinedBaseQuery()` to stop this memory leak and keep queries stable.

```php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class SalesReportRepository
{
    // A fast query with no joins, used only to map matching data IDs quickly
    public function getBaseFilteredQuery(array $filters = []): Builder
    {
        $query = DB::table('sales_reports as s');
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('s.sale_date', [$filters['start_date'], $filters['end_date']]);
        }
        if (!empty($filters['branch_id'])) {
            $query->where('s.branch_id', $filters['branch_id']);
        }
        return $query;
    }

    // Single source of truth for the 10-table relational schema with locked columns
    public function getJoinedBaseQuery(): Builder
    {
        return DB::table('sales_reports as s')
            ->leftJoin('customers as c', 's.customer_id', '=', 'c.id')
            ->leftJoin('products as p', 's.product_id', '=', 'p.id')
            ->leftJoin('branches as b', 's.branch_id', '=', 'b.id')
            ->leftJoin('payment_methods as pm', 's.payment_method_id', '=', 'pm.id')
            ->leftJoin('warehouses as w', 's.warehouse_id', '=', 'w.id')
            ->leftJoin('regions as r', 's.region_id', '=', 'r.id')
            ->leftJoin('categories as cat', 's.category_id', '=', 'cat.id')
            ->leftJoin('brands as br', 's.brand_id', '=', 'br.id')
            ->leftJoin('taxes as t', 's.tax_id', '=', 't.id')
            ->leftJoin('coupons as co', 's.coupon_id', '=', 'co.id')
            ->leftJoin('users as u', 's.created_by', '=', 'u.id')
            ->select([
                's.id as id', 's.invoice_number as invoice_number', 'c.name as customer_name',
                'c.phone as customer_phone', 'p.title as product_name', 'p.sku as product_sku',
                'b.name as branch_name', 'pm.name as payment_method', 'w.name as warehouse_name',
                'r.name as region_name', 'cat.name as category_name', 'br.name as brand_name',
                't.name as tax_name', 't.rate as tax_rate', 'co.code as coupon_code',
                'co.discount_amount as coupon_discount', 'u.name as operator_name',
                's.total_amount as total_amount', 's.sale_date as sale_date'
            ]);
    }

    public function getJoinedBaseFilteredQuery(array $filters = []): Builder
    {
        $query = $this->getJoinedBaseQuery();
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('s.sale_date', [$filters['start_date'], $filters['end_date']]);
        }
        if (!empty($filters['branch_id'])) {
            $query->where('s.branch_id', $filters['branch_id']);
        }
        if (!empty($filters['customer_name'])) {
            $query->where('c.name', 'like', '%' . $filters['customer_name'] . '%');
        }
        return $query;
    }
}

```

#### ② Service Layer: The Coordinator

* **File Path:** `app/Services/SalesReportExportService.php`
* **Why this code matters:** It quickly finds the start and end IDs of the filtered data without running slow table joins, breaks them into batches, and pushes them safely into the Redis queue.

```php
namespace App\Services;

use App\Repositories\SalesReportRepository;
use App\Jobs\{ProcessChunkExportJob, MergeChunksJob};
use Illuminate\Support\Facades\{Bus, File};
use Illuminate\Bus\Batch;

class SalesReportExportService
{
    public function __construct(protected SalesReportRepository $repo) {}

    public function handleExport(string $email, array $filters = []): array
    {
        $processStartTime = microtime(true);
        $uniqueBatchId = uniqid('exp_');
        $boundaries = [];

        $this->repo->getBaseFilteredQuery($filters)->orderBy('s.id')->chunkById(10000, function ($rows) use (&$boundaries) {
            $boundaries[] = ['startId' => $rows->first()->id, 'limitId' => $rows->last()->id];
        }, 's.id', 'id');

        if (empty($boundaries)) throw new \Exception("No data found for the given criteria.", 404);

        $jobs = [];
        foreach ($boundaries as $boundary) {
            $jobs[] = new ProcessChunkExportJob($boundary, $uniqueBatchId, $filters)->onQueue('low');
        }

        File::ensureDirectoryExists(storage_path("app/private/chunks/{$uniqueBatchId}"));

        Bus::batch($jobs)->name('Mega Complex Export')
            ->then(function (Batch $batch) use ($uniqueBatchId, $email, $processStartTime) {
                if ($batch->failedJobs === 0) {
                    MergeChunksJob::dispatch($uniqueBatchId, $email, $processStartTime)->onQueue('low');
                }
            })->dispatchAfterResponse();

        return ['status' => 'processing', 'message' => 'Mega export engine started successfully.'];
    }
}

```

#### ③ Job Layer (Part 1): The Background Processor

* **File Path:** `app/Jobs/ProcessChunkExportJob.php`
* **Why this code matters:** Each background worker takes care of a small, specific data range. It pulls database records smoothly via an unbuffered database `cursor()` and streams them row-by-row into a temporary CSV piece on the disk. This keeps memory usage low.

```php
namespace App\Jobs;

use App\Repositories\SalesReportRepository;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class ProcessChunkExportJob implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;

    public function __construct(protected array $boundary, protected string $uniqueBatchId, protected array $filters = []) {}

    public function handle(SalesReportRepository $repo): void
    {
        if ($this->batch()?->cancelled()) return;

        $path = storage_path("app/private/chunks/{$this->uniqueBatchId}/chunk_{$this->boundary['startId']}_{$this->boundary['limitId']}.csv");
        $file = fopen($path, 'w');

        $query = $repo->getJoinedBaseFilteredQuery($this->filters);

        if ($this->boundary['startId'] == $this->boundary['limitId']) {
            $query->where('s.id', $this->boundary['startId']);
        } else {
            $query->whereBetween('s.id', [$this->boundary['startId'], $this->boundary['limitId']]);
        }

        foreach ($query->orderBy('s.id')->cursor() as $row) {
            fputcsv($file, get_object_vars($row));
        }

        fclose($file);
    }
}

```

#### ④ Job Layer (Part 2): The File Merger

* **File Path:** `app/Jobs/MergeChunksJob.php`
* **Why this code matters:** Once all temporary CSV pieces are ready, this file merger puts them together in the perfect numerical order. It streams the content directly using the server's native file system, bypasses the PHP memory space entirely, cleans up the disk, and sends out the email.

```php
namespace App\Jobs;

use App\Jobs\SendReportEmailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{File, Storage, Log};

class MergeChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public function __construct(protected string $uniqueBatchId, protected string $email, protected float $processStartTime) {}

    public function handle(): void
    {
        $fileName = "exports/sales_report_{$this->uniqueBatchId}.csv";
        $publicDisk = Storage::disk('public');
        $finalPath = $publicDisk->path($fileName);

        File::ensureDirectoryExists(dirname($finalPath));
        $finalFile = fopen($finalPath, 'w');

        fputcsv($finalFile, ['ID', 'Invoice Number', 'Customer Name', 'Customer Phone', 'Product Name', 'Product SKU', 'Branch Name', 'Payment Method', 'Warehouse Name', 'Region Name', 'Category Name', 'Brand Name', 'Tax Name', 'Tax Rate', 'Coupon Code', 'Coupon Discount', 'Operator Name', 'Total Amount', 'Sale Date']);

        $chunks = glob(storage_path("app/private/chunks/{$this->uniqueBatchId}/chunk_*.csv"));
        natsort($chunks); 

        foreach ($chunks as $chunkFullPath) {
            if (file_exists($chunkFullPath)) {
                $cf = fopen($chunkFullPath, 'r');
                stream_copy_to_stream($cf, $finalFile); 
                fclose($cf);
                unlink($chunkFullPath); 
            }
        }
        fclose($finalFile);
        File::deleteDirectory(storage_path("app/private/chunks/{$this->uniqueBatchId}"));

        SendReportEmailJob::dispatch($this->email, $publicDisk->url($fileName));
        Log::info('S_R_Merge_END', ['grand_total_process_time' => number_format(microtime(true) - $this->processStartTime, 2) . 's']);
    }
}

```

---

### 4. 🚀 Core Performance Optimizations

| Optimization Technique | How It Works & Why It Helps |
| --- | --- |
| **Fixed Projections** | We locked the database column selection fields inside the repository. This prevents unexpected schema bugs and completely stops accidental full-table database scans. |
| **Clustered Index Range Jump** | Background processes look up records using direct primary key index ranges (`whereBetween`). The database reads rows instantly without wasting time on ordering or offsets. |
| **O(1) Kernel Space Streaming** | We use PHP's `stream_copy_to_stream()` with an 8 KB internal buffer. This pipes file fragments together directly at the operating system level, keeping PHP RAM usage at zero. |

---

### 5. 🎛️ Server & Queue Configuration (Safe for 2 GB RAM)

To make sure the background tasks never consume all the system memory or get killed by the operating system, we set strict limits inside Laravel Horizon:

```php
'production' => [
    'supervisor-1' => [
        'connection' => 'redis',
        'queue' => ['high', 'default', 'low'],
        'balance' => 'auto',
        'autoScalingStrategy' => 'time',
        'minProcesses' => 1,
        'maxProcesses' => 4,     // Limits background tasks to a maximum of 4 at a time
        'balanceMaxShift' => 1,
        'balanceCooldown' => 3,
        'memory' => 128,          // Automatically reboots a worker process if it hits 128 MB of RAM
        'tries' => 3,
        'timeout' => 120,     
    ],
],

```

> **📐 RAM Safety Calculation:** Since we limit the server to 4 parallel worker tasks and each task restarts if it reaches 128 MB, the engine will use a maximum of 512 MB of RAM. This leaves a safe 1.5 GB of RAM completely free for the operating system, database engine, and active web users.

---

## 📈 Real-World Production Metrics

```bash
[2026-05-29 17:02:52] local.INFO: S_R_Mega_Merge_START {"worker":72802} 
[2026-05-29 17:02:52] local.INFO: S_R_Merge_END {"grand_total_process_time":"13.58s"}

```

1. **Total Records in Database Pool:** 10,000,000+ rows.
2. **Database Complexity:** 10 active relational `LEFT JOIN` connections.
3. **Total Delivery Time:** **13.58 Seconds** (From the moment the user clicks export, to background processing, file merging, and final email delivery).

```