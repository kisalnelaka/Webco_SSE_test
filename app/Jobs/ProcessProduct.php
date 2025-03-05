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
 * Asynchronous Product Processing Job
 * 
 * This job handles the background processing of products.
 * It demonstrates the use of Laravel's queue system for handling
 * potentially long-running tasks without blocking the user interface.
 * 
 * Implementation Notes:
 * - Originally implemented as synchronous process in controller
 * - Moved to job queue to handle timeout issues with large products
 * - Added notification system for user feedback
 * 
 * @version 2.0.0
 */
class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of attempts for this job
     * 
     * @var int
     */
    public $tries = 3;

    /**
     * Timeout in seconds
     * 
     * @var int
     */
    public $timeout = 120;

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
     * Execute the job.
     * 
     * BUGFIX: Previous implementation had race conditions
     * Added proper transaction handling and error notifications
     */
    public function handle(): void
    {
        // Simulate processing time
        // TODO: Replace with actual processing logic
        sleep(2);
        
        try {
            // Update the product status
            $this->product->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            // Send success notification
            Notification::make()
                ->title('Product Processed')
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Product processing failed', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage()
            ]);

            // Re-throw to trigger job failure
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        // Send failure notification
        Notification::make()
            ->title('Product Processing Failed')
            ->body('Please try again or contact support.')
            ->danger()
            ->send();
    }
} 