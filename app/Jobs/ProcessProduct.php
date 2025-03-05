<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Product $product
    ) {}

    public function handle(): void
    {
        // Simulate processing time
        sleep(2);

        DB::transaction(function () {
            $this->product->description = $this->product->description . "\n[Processed at: " . now() . "]";
            $this->product->save();

            // Send notification (placeholder)
            // event(new ProductProcessed($this->product));
        });
    }
} 