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
            $table->unsignedBigInteger('tenant_id')->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
