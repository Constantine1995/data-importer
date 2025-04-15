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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('g_number')->index();
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->bigInteger('barcode');
            $table->decimal('total_price', 12, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->decimal('promo_code_discount', 12, 2)->nullable();
            $table->string('warehouse_name');
            $table->string('country_name');
            $table->string('oblast_okrug_name');
            $table->string('region_name');
            $table->integer('income_id');
            $table->string('sale_id')->index();
            $table->string('odid')->nullable();
            $table->decimal('spp', 5, 2);
            $table->decimal('for_pay', 12, 2);
            $table->decimal('finished_price', 12, 2);
            $table->decimal('price_with_disc', 12, 2);
            $table->bigInteger('nm_id')->index();
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->boolean('is_storno')->nullable();
            $table->timestamps();

            $table->index(['date', 'warehouse_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
