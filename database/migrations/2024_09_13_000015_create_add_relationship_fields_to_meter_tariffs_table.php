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
        Schema::table('meter_tariffs', function (Blueprint $table) {
            $table->unsignedBigInteger('meter_id')->after('id');
            $table->foreign('meter_id')->references('id')->on('meters')->onDelete('cascade');
            $table->unsignedBigInteger('tariff_id')->after('id');
            $table->foreign('tariff_id')->references('id')->on('tariffs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_tariffs', function (Blueprint $table) {
            $table->dropForeign(['meter_id']);
            $table->dropColumn('meter_id');
            $table->dropForeign(['tariff_id']);
            $table->dropColumn('tariff_id');
        });
    }
};