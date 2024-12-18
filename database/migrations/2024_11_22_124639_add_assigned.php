<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('opportunity_id');
                $table->boolean('is_assigned')->default(false)->after('assigned_to');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumns('transactions', ['assigned_to'])) {
                $table->dropColumn('assigned_to');
                $table->dropColumn('is_assigned');

            }
        });
    }
};
