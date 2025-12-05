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
