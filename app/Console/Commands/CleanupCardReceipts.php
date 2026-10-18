<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupCardReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipts:cleanup-card {--days=7 : Number of days after which receipts should be removed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically delete card-to-card receipt images older than 7 days from storage to preserve server disk space';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Scanning card-to-card receipts created on or before: {$cutoffDate->toDateTimeString()}");

        $payments = Payment::where('gateway', 'card_to_card')
            ->whereNotNull('receipt_image')
            ->where('created_at', '<=', $cutoffDate)
            ->get();

        $deletedCount = 0;

        foreach ($payments as $payment) {
            $imagePath = $payment->receipt_image;

            if ($imagePath) {
                if (Storage::disk('local')->exists($imagePath)) {
                    Storage::disk('local')->delete($imagePath);
                }
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $payment->update([
                'receipt_image' => null,
                'admin_notes' => trim(($payment->admin_notes ?? '') . ' [فیش واریزی پس از ۱ هفته به صورت خودکار پاکسازی شد]'),
            ]);

            $deletedCount++;
        }

        $this->info("Deleted {$deletedCount} old card-to-card receipt(s).");
        Log::info("Card-to-card receipt cleanup: deleted {$deletedCount} receipt images older than {$days} days.");

        return Command::SUCCESS;
    }
}
