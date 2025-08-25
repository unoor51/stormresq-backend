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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('people_count')->default(0); // Number of people
            $table->boolean('pets')->default(0)->after('people_count');          // 0 = No, 1 = Yes
            $table->boolean('disabled')->default(0)->after('pets');              // 0 = No, 1 = Yes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['people_count', 'pets', 'disabled']);
        });
    }
};
