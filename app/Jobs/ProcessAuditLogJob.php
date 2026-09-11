<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Tries and timeout settings for high-scale queues.
     */
    public int $tries = 3;
    public int $timeout = 60;

    protected array $auditData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $auditData)
    {
        $this->auditData = $auditData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            AuditLog::create($this->auditData);
        } catch (\Throwable $e) {
            Log::error("ProcessAuditLogJob error: {$e->getMessage()}", [
                'data' => $this->auditData,
            ]);
        }
    }
}
