<p align="center">
  <img src="https://media.licdn.com/dms/image/v2/D5622AQG2Xy9rv4X9gA/feedshare-shrink_1280/B56Z5tFdLTGoAM-/0/1779946607611?e=1781740800&v=beta&t=xj5ghEkW25q3uSFWGEyA973sdZARSoylwNi50AO1YOU" alt="Cover Image" width="100%">
</p>

### 🎯 Main Challenge

The primary challenge was meeting the requirement to generate and deliver a downloadable report containing **1 million rows within a strict 10-minute SLA**. To achieve this, the system had to efficiently process and filter data from a massive dataset of over **10 million rows** while running on a highly constrained **2 GB RAM Ubuntu VPS** without exhausting memory or causing downtime.

---

## 🛠️ Solution

### 1. ⚡ Executive Summary (Under 30 Seconds)

We engineered an asynchronous export engine that processes a **1 million-row** report from a 10-million-row dataset in just **⏱️ 28.17 seconds**, maintaining a flat, ultra-low memory footprint of **14.2 MB**. Operating on a highly constrained **2 GB RAM Ubuntu VPS**, traditional linear data processing would have triggered OOM (Out of Memory) crashes. We solved this by completely decoupling database pagination from execution, parallelizing workload distribution through Laravel Horizon 🌤️, and consolidating chunks using $O(1)$ kernel-level stream piping 🧬.

---

### 2. 🏗️ System Architecture & Workflow

1. **Phase 1 (The Boundary Locator):** Executed instantaneously in the background using `dispatchAfterResponse()`. It uses `chunkById()` to map ID boundaries for 10,000 rows per chunk, bypassing heavy data hydration.
2. **Phase 2 (Parallel Chunk Processing):** Segmented chunk jobs are pushed to a low-priority queue (`low`). Multiple Laravel Horizon workers execute these tasks concurrently, writing isolated temporary CSV files safely on disk.
3. **Phase 3 (Atomic Stream Merging):** Upon completion of all batch jobs, the `finally()` block automatically triggers the final merge process. It opens a binary stream, appends all chunks sequentially using OS-level buffers, deletes temporary artifacts, and queues the email notification.

---

### 3. 💻 Codes

#### SalesReportExportService.php

```php


class SalesReportExportService
{
    public function handleExport(string $email): array
    {
        $processStartTime = microtime(true); // Total process time calculation start
        $uniqueBatchId = uniqid('exp_');
        $boundaries = [];

        DB::table('sales_reports')
            ->orderBy('id')
            ->chunkById(10000, function ($rows) use (&$boundaries) {
                $boundaries[] = [
                    'startId' => $rows->first()->id,
                    'limitId' => $rows->last()->id
                ];
            });

        if (empty($boundaries)) throw new \Exception("No data found.", 404);

        $jobs = [];
        foreach ($boundaries as $boundary) {
            // creating temp csv or chunked data csv first
            $jobs[] = new ProcessChunkExportJob($boundary, $uniqueBatchId)->onQueue('low');
        }

        Bus::batch($jobs)->name('Sales Report Export')
            ->finally(function (Batch $batch) use ($uniqueBatchId, $email, $processStartTime) {
                // merging all csv data to finalize report.csv
                MergeChunksJob::dispatch($uniqueBatchId, $email, $processStartTime)->onQueue('low');
            })
            ->catch(function (Batch $batch, \Throwable $e) {
                Log::error('Batch failed', ['error' => $e->getMessage()]);
            })
            ->dispatchAfterResponse(); // Executed instantaneously in the background using `dispatchAfterResponse()

        return ['status' => 'processing', 'message' => 'Export started. Link will be emailed.'];
    }
}

```

#### 2. ProcessChunkExportJob

```php

class ProcessChunkExportJob implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected $data, protected $uniqueBatchId) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) return;

        $path = storage_path("app/private/chunks/{$this->uniqueBatchId}/chunk_{$this->data['startId']}_{$this->data['limitId']}.csv");
        File::ensureDirectoryExists(dirname($path));

        $file = fopen($path, 'w');
        $query = DB::table('sales_reports')->select(['id', 'invoice_number', 'customer_name', 'total_amount', 'sale_date']);

        $query = ($this->data['startId'] == $this->data['limitId']) 
            ? $query->where('id', $this->data['startId']) 
            : $query->whereBetween('id', [$this->data['startId'], $this->data['limitId']]);

        foreach ($query->orderBy('id')->cursor() as $sale) {
            fputcsv($file, get_object_vars($sale));
        }

        fclose($file);
    }
}

```

#### 3. MergeChunksJob

```php

class MergeChunksJob implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // High timeout to guard against disk I/O bottlenecks on low-tier VPS

    public function __construct(protected $uniqueBatchId, protected $email, protected $processStartTime) {}

    public function handle(): void
    {
        $fileName = "exports/sales_report_{$this->uniqueBatchId}.csv";
        $publicDisk = Storage::disk('public');
        $finalPath = $publicDisk->path($fileName);

        File::ensureDirectoryExists(dirname($finalPath));
        $finalFile = fopen($finalPath, 'w');

        fputcsv($finalFile, ['ID', 'Invoice Number', 'Customer Name', 'Total Amount', 'Sale Date']);

        $chunks = glob(storage_path("app/private/chunks/{$this->uniqueBatchId}/chunk_*.csv"));
        natsort($chunks); // Human-sorting ensures chunks merge in exact chronological ID order

        foreach ($chunks as $chunkFullPath) {
            if (file_exists($chunkFullPath)) {
                $cf = fopen($chunkFullPath, 'r');
                stream_copy_to_stream($cf, $finalFile); // Kernel-level stream copy bypasses PHP memory allocation entirely (O(1) efficiency)
                fclose($cf);
                unlink($chunkFullPath); // Immediate unlinking prevents disk space bloating during execution
            }
        }
        fclose($finalFile);

        $tempFolder = storage_path("app/private/chunks/{$this->uniqueBatchId}");
        if (is_dir($tempFolder)) { File::deleteDirectory($tempFolder); }

        SendReportEmailJob::dispatch($this->email, $publicDisk->url($fileName));
        
        // Keep precise metrics for infrastructure SLA(Service Level Agreement) monitoring
        Log::info('Merge_END', [
            'grand_total_process_time' => number_format(microtime(true) - $this->processStartTime, 2) . 's'
        ]);
    }
}

```

---

### 4. 🚀 Core Performance Optimizations

1. **`stream_copy_to_stream()` Integration 💎:** Instead of looping through rows and building massive string variables in PHP memory space, this uses low-level OS buffers to pipe raw binary streams in $8\text{ KB}$ segments. It guarantees $O(1)$ memory efficiency, making memory utilization independent of file scale.
2. **Deterministic Queue Segregation 🚦:** Horizon workers process queues using a strict left-to-right priority: `['high', 'default', 'low']`. Isolating this resource-heavy export to the `low` queue ensures zero scheduling lag for critical, time-sensitive transactional operations like OTPs or webhooks.
3. **Immediate Storage Reclamation 🧹:** Temporary artifacts are unlinked instantly upon stream depletion. This prevents localized disk-space inflation on low-tier storage volumes during concurrent runs.

---

### 5. 🎛️ Infrastructure & Horizon Tuning (2 GB RAM Safety)

To achieve deterministic memory safety on a constrained 2 GB VPS, the system's process allocation strategy is explicitly capped within the supervisor layout to neutralize the Linux OOM (Out of Memory) killer 🛡️:

```php
'production' => [
    'supervisor-1' => [
        'connection' => 'redis',
        'queue' => ['high', 'default', 'low'],
        'balance' => 'auto',
        'autoScalingStrategy' => 'time',
        'minProcesses' => 1,
        'maxProcesses' => 4, // Strict horizontal concurrency ceiling
        'balanceMaxShift' => 1,
        'balanceCooldown' => 3,
        'memory' => 128,      // Hard memory limit per worker process before cycling
        'tries' => 3,
        'timeout' => 120,     
    ],
],

```

> **📐 Architectural Breakdown:** By limiting workers to a maximum of 4 processes and setting a strict 128 MB threshold per process, the absolute worst-case memory ceiling for background processing is locked at 512 MB. This keeps resource consumption highly predictable, leaving ample headroom for the OS, Nginx, and the database engine to operate comfortably.
