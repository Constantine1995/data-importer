<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->integer('income_id')->index();
            $table->string('number');
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->bigInteger('barcode');
            $table->integer('quantity');
            $table->decimal('total_price', 12, 2);
            $table->date('date_close');
            $table->string('warehouse_name');
            $table->bigInteger('nm_id')->index();
            $table->timestamps();

            $table->index(['date', 'warehouse_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
