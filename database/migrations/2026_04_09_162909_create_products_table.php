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
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->unsignedBigInteger('shop_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 2);
            $table->integer('bought_count')->unsigned()->default(0);
            $table->string('icon_url', 500)->nullable();
            $table->string('item_id', 255);
            $table->json('item_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('shop_id')->references('id_shop')->on('shops')->onDelete('cascade');
            $table->index('shop_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
