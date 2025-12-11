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
        // Just ensure variation_id column exists (don't change primary key)
        if (!Schema::hasColumn('order_product', 'variation_id')) {
            Schema::table('order_product', function (Blueprint $table) {
                $table->foreignId('variation_id')->nullable()->after('product_id')->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_product', 'variation_id')) {
            Schema::table('order_product', function (Blueprint $table) {
                $table->dropForeign(['variation_id']);
                $table->dropColumn('variation_id');
            });
        }
    }
};
