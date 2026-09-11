<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    protected array $recipients;
    protected string $message;

    public function __construct(array $recipients, string $message)
    {
        $this->recipients = $recipients;
        $this->message = $message;
    }

    public function handle(): void
    {
        Log::info("SendSmsNotificationJob sending message to " . count($this->recipients) . " recipients.");
    }
}
