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
        Schema::table('rescuers', function (Blueprint $table) {
            //
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_token')->nullable(); // used for custom verification
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rescuers', function (Blueprint $table) {
            //
            $table->dropColumn('email_verified_at');
            $table->dropColumn('verification_token');

        });
    }
};
