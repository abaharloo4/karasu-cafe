<?php

namespace App\Jobs;

use App\Services\Sms\SmsGatewayInterface;
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
    public int $timeout = 15;

    public function __construct(
        public string $type,
        public string $phone,
        public string $contentOrCode,
        public array $args = []
    ) {}

    public function handle(SmsGatewayInterface $smsGateway): void
    {
        try {
            if ($this->type === 'pattern') {
                $smsGateway->sendPattern($this->phone, $this->contentOrCode, $this->args);
            } else {
                $smsGateway->sendNotification($this->phone, $this->contentOrCode);
            }
        } catch (\Throwable $e) {
            Log::warning("SendSmsNotificationJob failed: " . $e->getMessage());
        }
    }
}
