<?php

namespace App\Jobs;

use App\Models\Ingredient;
use App\Models\StockNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendLowStockNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $merchantEmail = $this->ingredient->merchant->email ?? 'default@example.com';

        Notification::route('mail', $merchantEmail)
            ->notify(new LowStockNotification($this->ingredient));
    }

}
