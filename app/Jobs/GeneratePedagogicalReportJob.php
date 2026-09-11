<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePedagogicalReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300; // 5 minutes max for complex reports

    protected array $reportParams;
    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $reportParams, int $userId)
    {
        $this->reportParams = $reportParams;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("GeneratePedagogicalReportJob starting for user {$this->userId}", $this->reportParams);
        // Process heavy MINEDH PDF/Excel report compilation in background
    }
}
