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
        Schema::table('order_product', function (Blueprint $table) {
            // Drop existing primary key
            $table->dropPrimary(['order_id', 'product_id']);
        });
        
        Schema::table('order_product', function (Blueprint $table) {
            // Add variation_id column
            $table->foreignId('variation_id')->nullable()->after('product_id')->constrained()->onDelete('cascade');
            // Create new primary key including variation_id
            $table->primary(['order_id', 'product_id', 'variation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropPrimary(['order_id', 'product_id', 'variation_id']);
        });
        
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropForeign(['variation_id']);
            $table->dropColumn('variation_id');
            $table->primary(['order_id', 'product_id']);
        });
    }
};
