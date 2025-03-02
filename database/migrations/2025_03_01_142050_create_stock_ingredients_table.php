<?php

use App\Models\Ingredient;
use App\Models\Merchant;
use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ingredient::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Merchant::class)->index()->constrained()->cascadeOnDelete();
            $table->decimal('quantity');
            $table->foreignIdFor(Unit::class)->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('stock_ingredients');
    }
}
