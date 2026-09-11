<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateClassGradesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    protected int $classId;
    protected int $term;
    protected int $year;

    public function __construct(int $classId, int $term, int $year)
    {
        $this->classId = $classId;
        $this->term = $term;
        $this->year = $year;
    }

    public function handle(): void
    {
        Log::info("RecalculateClassGradesJob executing for class {$this->classId}, T{$this->term}-{$this->year}");
    }
}
