<?php

namespace App\Services\Sms;

interface SmsDriverInterface
{
    /**
     * Send an SMS to a single recipient.
     *
     * @param  string  $to    E.164 phone number (e.g. +258840000000)
     * @param  string  $message
     * @return array{success: bool, provider_id?: string, error?: string}
     */
    public function send(string $to, string $message): array;
}