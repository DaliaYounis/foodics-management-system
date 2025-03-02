<?php

use App\Models\Ingredient;
use App\Models\Merchant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ingredient::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Merchant::class)->index()->constrained()->cascadeOnDelete();
            $table->timestamp('send_at')->nullable();
            $table->boolean('is_notified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_notifications');
    }
}
