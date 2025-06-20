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
        Schema::table('evacuees', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('needs_disabled');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evacuees', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
