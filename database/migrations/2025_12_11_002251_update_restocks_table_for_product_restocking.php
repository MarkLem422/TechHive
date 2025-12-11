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
        Schema::table('restocks', function (Blueprint $table) {
            // Add product_id (nullable for backward compatibility with existing variation restocks)
            $table->foreignId('product_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Add quantity_added column (copy from quantity if it exists)
            $table->integer('quantity_added')->default(0)->after('quantity');
            
            // Add new fields
            $table->decimal('cost_per_unit', 10, 2)->nullable()->after('quantity_added');
            $table->decimal('total_cost', 10, 2)->nullable()->after('cost_per_unit');
            $table->integer('previous_stock')->nullable()->after('total_cost');
            $table->integer('new_stock')->nullable()->after('previous_stock');
            $table->timestamp('restocked_at')->nullable()->after('new_stock');
        });
        
        // Copy data from quantity to quantity_added
        \Illuminate\Support\Facades\DB::statement('UPDATE restocks SET quantity_added = quantity WHERE quantity_added = 0');
        
        // Make quantity nullable (keep for backward compatibility)
        Schema::table('restocks', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Copy back from quantity_added before dropping
        \Illuminate\Support\Facades\DB::statement('UPDATE restocks SET quantity = COALESCE(quantity, quantity_added)');
        
        Schema::table('restocks', function (Blueprint $table) {
            // Restore quantity if needed
            $table->integer('quantity')->default(0)->change();
            
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity_added', 'cost_per_unit', 'total_cost', 'previous_stock', 'new_stock', 'restocked_at']);
        });
    }
};
