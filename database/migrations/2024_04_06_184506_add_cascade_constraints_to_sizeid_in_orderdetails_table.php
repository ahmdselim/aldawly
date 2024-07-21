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
        Schema::table('orderdetails', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign('orderdetails_sizeid_foreign');

            // Add a new foreign key constraint with ON DELETE CASCADE and ON UPDATE CASCADE
            $table->foreign('sizeid')
                ->references('id')
                ->on('sizes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('orderdetails', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign('orderdetails_sizeid_foreign');

            // Re-add the foreign key constraint without ON DELETE CASCADE and ON UPDATE CASCADE
            $table->foreign('sizeid')
                ->references('id')
                ->on('sizes');
        });
    }
};
