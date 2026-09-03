<?php

namespace App\Notifications\Channels;

use App\Models\ParentModel;
use App\Models\User;
use App\Services\Sms\SmsManager;
use Illuminate\Notifications\Messages\Message;

class SmsMessage
{
    public function __construct(public string $content) {}

    public function content(): string
    {
        return $this->content;
    }
}