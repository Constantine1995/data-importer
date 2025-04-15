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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('g_number')->index();
            $table->dateTime('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->bigInteger('barcode');
            $table->decimal('total_price', 12, 2);
            $table->integer('discount_percent');
            $table->string('warehouse_name');
            $table->string('oblast');
            $table->integer('income_id');
            $table->string('odid');
            $table->bigInteger('nm_id')->index();
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->boolean('is_cancel');
            $table->date('cancel_dt')->nullable();
            $table->timestamps();

            $table->index(['date', 'warehouse_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
