<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->constrained()->onDelete('cascade');
            $table->uuid('user_id')->nullable();
            $table->json('items');
            $table->decimal('total', 12, 2);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamps();

            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
