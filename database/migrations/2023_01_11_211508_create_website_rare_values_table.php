<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('website_rare_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->integer('item_id')->nullable()->index();
            $table->string('name')->index();
            $table->string('credit_value')->nullable();
            $table->string('currency_value')->nullable();
            $table->string('currency_type')->default('diamonds');
            $table->string('furniture_icon');
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('website_rare_value_categories')
                ->cascadeOnDelete();

            $table->foreign('item_id')
                ->references('item_ids')
                ->on('catalog_items')
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_rare_values');
    }
};