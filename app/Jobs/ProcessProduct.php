<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;

/**
 * Handles product processing in the background.
 * Updates status and sends notifications.
 */
class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Retry settings
    public $tries = 3;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     * 
     * BUGFIX: Previously passed only product ID
     * Changed to pass entire model to leverage Laravel's serialization
     */
    public function __construct(
        protected Product $product
    ) {}

    /**
     * Main job steps:
     * - Updates product
     * - Sends notifications
     * - Handles errors
     */
    public function handle(): void
    {
        try {
            // Mark as done
            $this->product->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            // Tell user it worked
            Notification::make()
                ->title('Product Processed')
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Save error info
            logger()->error('Product processing failed', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            
            // Tell user it failed
            Notification::make()
                ->title('Product Processing Failed')
                ->body('Please try again or contact support.')
                ->danger()
                ->send();
            
            throw $e;
        }
    }
} 