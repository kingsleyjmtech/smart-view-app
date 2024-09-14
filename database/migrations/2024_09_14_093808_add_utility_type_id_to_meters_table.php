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
        Schema::table('meters', function (Blueprint $table) {
            $table->unsignedBigInteger('utility_type_id')->after('id');
            $table->foreign('utility_type_id')->references('id')->on('utility_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropForeign(['utility_type_id']);
            $table->dropColumn('utility_type_id');
        });
    }
};
