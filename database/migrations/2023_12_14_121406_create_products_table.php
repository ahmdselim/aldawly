<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('productName');
            $table->string('productDescription');
            $table->string('color');
            // $table->integer('Coach_price');
            // $table->integer('Store_price');
            // $table->integer('Player_price');
            $table->string('productImage')->nullable();
            $table->foreignId('cat_id')->references('id')->on('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('subcat', ['men', 'women','other']);
            $table->enum('has_offer', [0,1])->default('0');

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
        Schema::dropIfExists('products');
    }
};
