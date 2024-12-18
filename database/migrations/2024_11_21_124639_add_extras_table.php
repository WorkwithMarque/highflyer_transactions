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
            if (! Schema::hasColumn('transactions', 'stage_id')) {
                $table->string('stage_id')->nullable()->after('tag');
            }
            if (! Schema::hasColumn('transactions', 'order_status')) {
                $table->string('order_status')->nullable()->after('stage_id');
            }

            if (! Schema::hasColumn('transactions', 'production_status')) {
                $table->string('production_status')->nullable()->after('order_status');
            }

            if (! Schema::hasColumn('transactions', 'date_produced')) {
                $table->string('date_produced')->nullable()->after('production_status');
            }

            if (! Schema::hasColumn('transactions', 'is_produced')) {
                $table->boolean('is_produced')->default(false)->after('date_produced');
            }
            if (! Schema::hasColumn('transactions', 'opportunity_id')) {
                $table->string('opportunity_id')->nullable()->after('is_produced');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumns('transactions', ['stage_id', 'order_status', 'production_status'])) {
                $table->dropColumn('stage_id');
                $table->dropColumn('order_status');
                $table->dropColumn('production_status');
                $table->dropColumn('date_produced');
                $table->dropColumn('is_produced');
                $table->dropColumn('opportunity_id');


            }
        });
    }
};
