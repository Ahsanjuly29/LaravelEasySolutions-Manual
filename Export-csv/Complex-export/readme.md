<p align="center">
  <img src="https://media.licdn.com/dms/image/v2/D5622AQGdHxl72Cpcmw/feedshare-shrink_800/B56Z5zv8SkK4Ac-/0/1780058408349?e=1781740800&v=beta&t=JOfVE6RaMKWys5gQbbAd2i5uS0yv2yvM4-z3wbo5I_E" alt="Cover Image" width="100%">
</p>

# 🚀 Mega Complex Export Engine

> **⚡ The Architectural Feat:** $10\text{M}+$ (১ কোটি) ডেটার বিশাল পুল থেকে Multiple(১০টি) টেবিল জয়েনসহ যেকোনো জটিল সেলস রিপোর্ট ফিল্টার করে, মাত্র **১৩.৫৮ সেকেন্ডে** সম্পূর্ণ প্রসেস শেষ করে ইউজারের ইমেইলে ডাউনলোডেবল CSV পৌঁছে দেয় এই হাইপার-অপ্টিমাইজড ইঞ্জিন।

---

### 🎯 Main Challenge: Deterministic SLA Under High-Cardinality Joins & Sparse Data Distribution

1. **The Core Bottleneck:** $10\text{M}+$ (কোটি) ডাটার পুল থেকে রিয়েল-টাইม ডাইনামিক ফিল্টার অ্যাপ্লাই করে একটি সুনির্দিষ্ট উপসেট (যেমন: ১০,০০০ থেকে ৪৩,০০০ ডাটা) ছেঁকে বের করা, যেখানে প্রতিটা রো-এর সাথে **১০টি টেবিল `LEFT JOIN`** অবস্থায় যুক্ত।
2. **Infrastructure Constraint:** প্রোডাকশন রানটাইম এনভায়রনমেন্টটি একটি হাইলি-কনস্ট্রেইন্ড **2 GB RAM Ubuntu VPS**। ট্র্যাডিশনাল চ্যাঙ্কিং বা অফসেট-বেসড মেথড ব্যবহার করলে মাইএসকিউএল মেমোরিতে বিশাল ভার্চুয়াল টেবিল তৈরি করে, যা ওওএম (Out of Memory) ক্র্যাশ ঘটায়। 
3. **The Target (SLA):** ডাটাবেসের ওপর জিরো-ডাউনটাইম নিশ্চিত করে সম্পূর্ণ প্রসেসটি (ফিল্টারিং, চ্যাঙ্কিং, জয়েনিং, কার্নেল-লেভেল ফাইল মার্জিং এবং নোটিফিকেশন) **১৫ সেকেন্ডের কঠোর SLA**-এর মধ্যে সম্পন্ন করা।

---

## 🛠️ Solution

### 1. ⚡ Executive Summary
আমরা একটি **"No-Join Boundary Locator + Zero-Filter Job Execution"** আর্কিটেকচারাল প্যাটার্ন ইমপ্লিমেন্ট করেছি। এটি ডাটাবেস লেভেলের হেভি কুয়েরি ওভারহেডকে পিএইচপি মেমোরি স্পেস থেকে সম্পূর্ণ ডিকাপল করে। ফলস্বরূপ, কোটি ডাটা ফিল্টার ও প্রসেস হয়ে **মাত্র ১৩.৫৮ সেকেন্ডে** ইউজারের ইমেইলে নোটিফিকেশন চলে যায়, যেখানে ব্যাকগ্রাউন্ড র‍্যাম কনজাম্পশন থাকে একদম ফ্ল্যাট সিঙ্গেল-ডিজিট লাইনে।

---

### 2. 🏗️ System Architecture & Workflow


```
        [🔥 HTTP EXPORT REQUEST]
                   │
                   ▼ (Phase 1: Fast Boundary Mapping)

```

┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 📂 app/Services/SalesReportExportService.php                                           │
│ ├─► Executes `getBaseFilteredQuery()` [💥 Strict Rule: NO JOINS, High-Speed Index Scan]│
│ └─► Resolves Sparse ID gaps seamlessly via `chunkById(10000)`                          │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
│
▼ (Pushes Deterministic PK Ranges to Redis)
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 🌤️ Laravel Horizon Workers (Parallelized Execution Pipeline)                            │
│ ├─► Connection: Redis  |  Queue: `low`  |  Scale: Max 4 Concurrent Worker Processes   │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
│
┌──────────────────────┴──────────────────────┐
▼ (Concurrent Worker Segment 1)               ▼ (Concurrent Worker Segment N)
┌──────────────────────────────────────────┐  ┌──────────────────────────────────────────┐
│ 📂 app/Jobs/ProcessChunkExportJob.php    │  │ 📂 app/Jobs/ProcessChunkExportJob.php    │
│ ├─► Locks: `whereBetween('s.id')`        │  │ ├─► Locks: `whereBetween('s.id')`        │
│ ├─► Architecture: Fixed Repository Select│  │ ├─► Architecture: Fixed Repository Select│
│ └─► Outputs: `chunk_1_10000.csv`         │  │ └─► Outputs: `chunk_N_NNNNN.csv`         │
└───────────────────┬──────────────────────┘  └───────────────────┬──────────────────────┘
│                                             │
└──────────────────────┬──────────────────────┘
│ (On Absolute Queue Batch Completion)
▼ (Phase 3: Native Consolidation)
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 📂 app/Jobs/MergeChunksJob.php                                                         │
│ ├─► File Sorting: Orders arrays using `natsort()` to preserve chronological integrity  │
│ ├─► Zero-RAM Processing: Pipes binary fragments directly via `stream_copy_to_stream()`  │
│ ├─► Disk Optimization: Triggers immediate directory purge to reclaim local storage space│
│ └─► Event Trigger: Dispatches `SendReportEmailJob` with public storage download URL    │
└────────────────────────────────────────────────────────────────────────────────────────┘


---

### 3. 💻 Core Codebase Architecture

#### ① Repository Layer: Single Source of Truth
* **File Path:** `app/Repositories/SalesReportRepository.php`
* **Core Logic:** কুয়েরি বিল্ডারে রানটাইম মেথড থেকে রিটার্ন নিয়ে মাঝপথে আলাদা করে সিলেক্ট বসালে ওল্ড স্কোপ বা উইন্ডো অবজেক্ট ওভাররাইট হয়ে যায়। এর ফলে কলাম অ্যাম্বিগুয়েশনের কারণে ৪৩,০০০ ডাটার জায়গায় ফুল টেবিল স্ক্যান (৯ লাখ+ ডাটা) ট্রিগার হতো। আমরা সিলেকশন মেথডকে সরাসরি `getJoinedBaseQuery()` এর ভেতর ফিক্সড লক করে এই সائلেন্ট ওভারহেড চিরতরে নির্মূল করেছি।

```php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class SalesReportRepository
{
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

#### ② Service Layer: Chunk Coordinator

* **File Path:** `app/Services/SalesReportExportService.php`

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

#### ③ Job Layer (Part 1): Parallel Worker

* **File Path:** `app/Jobs/ProcessChunkExportJob.php`

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

#### ④ Job Layer (Part 2): Native Stream Aggregator

* **File Path:** `app/Jobs/MergeChunksJob.php`

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

| অপ্টিমাইজেশন টেকনিক | মেকানিজম এবং আর্কিটেকচারাল ইমপ্যাক্ট |
| --- | --- |
| **Fixed Projections** | সিলেক্ট কোয়েরি মেথডকে রিপোজিটরিতে হার্ড-লক করে ডাইনামিক স্কোপ ওভাররাইট বাগ এবং আননেসেসারি ফুল-টেবিল স্ক্যান চিরতরে নির্মূল করা হয়েছে। |
| **Clustered Index Range Jump** | কোনো প্রকার অফসেট ওভারহেড বা মেমোরি সর্টিং ছাড়া প্রসেসগুলো সরাসরি মাইএসকিউএল প্রাইমারি ক্লাস্টারড ইনডেক্স ট্রি থেকে ডেটা রিড করে। |
| **$O(1)$ Kernel Space Streaming** | `stream_copy_to_stream()` কার্নেল বাফার (8 KB) ব্যবহার করে সরাসরি ডিস্ক-টু-ডিস্ক বাইনারি পাইপিং সম্পন্ন করে, ফলে পিএইচপি মেমরিতে কোনো লোড পড়ে না। |

---

### 5. 🎛️ Infrastructure & Horizon Configuration ($2\text{ GB RAM}$ Tuning)

শেয়ার্ড ও লিমিটেড ২ জিবি র‍্যামের প্রোডাকশন সার্ভারে মেমোরি সিলিং কঠোরভাবে রেগুলারাইজ করা হয়েছে:

```php
'production' => [
    'supervisor-1' => [
        'connection' => 'redis',
        'queue' => ['high', 'default', 'low'],
        'balance' => 'auto',
        'autoScalingStrategy' => 'time',
        'minProcesses' => 1,
        'maxProcesses' => 4,     // Rigid horizontal concurrency ceiling
        'balanceMaxShift' => 1,
        'balanceCooldown' => 3,
        'memory' => 128,          // Automatically recycles the worker process upon hitting 128 MB
        'tries' => 3,
        'timeout' => 120,     
    ],
],

```

> **📐 Structural Sizing Metric:** সর্বোচ্চ ৪টি প্যারালাল ওয়ার্কার এবং প্রতি প্রসেসে ১২৮ MB এর থ্রেশহোল্ড থাকার কারণে ব্যাকগ্রাউন্ডেড মেমোরি সিলিং হবে সর্বোচ্চ ৫১২ MB। এর ফলে হোস্ট ওএস, রেডিস এবং মূল ডাটাবেস ইঞ্জিন বাকি ১.৫ GB মেমোরি নিয়ে অত্যন্ত সাচ্ছন্দ্যে হাই-থ্রুটপুট রান করতে পারবে।

---

## 📈 Enterprise Production Metrics

```bash
[2026-05-29 17:02:52] local.INFO: S_R_Mega_Merge_START {"worker":72802} 
[2026-05-29 17:02:52] local.INFO: S_R_Merge_END {"grand_total_process_time":"13.58s"}

```

1. **Dataset Pool:** $10,000,000+$ Records
2. **Structural Dependency:** 10 Active Database Relational `LEFT JOIN` Operations
3. **Grand Total SLA Delivery Time:** **13.58 Seconds** (End-to-End থেকে শুরু করে মেইলিং ডেলিভারি পর্যন্ত)

```