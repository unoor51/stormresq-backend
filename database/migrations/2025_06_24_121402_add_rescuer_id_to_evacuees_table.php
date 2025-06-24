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
            $table->foreignId('rescuer_id')->nullable()->constrained('rescuers')->onDelete('set null');
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
            $table->dropForeign(['rescuer_id']);
            $table->dropColumn('rescuer_id');
        });
    }
};
