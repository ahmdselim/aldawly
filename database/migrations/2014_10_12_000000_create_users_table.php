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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // $table->string('type');
            $table->enum('type', ['coach', 'store','player']);
            $table->string('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('phone_number');
            $table->boolean('active');

            // this data for coatch
            $table->string('id_image')->nullable();
            // this for only store
            $table->string('tax_card_image')->nullable();
            $table->string('store_image')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
