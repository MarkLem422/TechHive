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
            // Drop existing foreign key constraints first
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
            
            // Drop existing primary key
            $table->dropPrimary(['order_id', 'product_id']);
            
            // Add variation_id column
            $table->foreignId('variation_id')->nullable()->after('product_id')->constrained()->onDelete('cascade');
            
            // Recreate foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
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
            // Drop the composite primary key
            $table->dropPrimary(['order_id', 'product_id', 'variation_id']);
            
            // Drop all foreign keys
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variation_id']);
            
            // Drop variation_id column
            $table->dropColumn('variation_id');
            
            // Recreate original foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Recreate original primary key
            $table->primary(['order_id', 'product_id']);
        });
    }
};
