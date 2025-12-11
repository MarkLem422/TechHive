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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('status');
            $table->string('shipping_email')->nullable()->after('shipping_name');
            $table->string('shipping_phone')->nullable()->after('shipping_email');
            $table->text('shipping_address')->nullable()->after('shipping_phone');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_address');
            $table->decimal('tax', 10, 2)->default(0)->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'shipping_cost', 'tax']);
        });
    }
};
