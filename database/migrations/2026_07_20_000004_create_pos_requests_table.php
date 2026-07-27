<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->constrained()->onDelete('cascade');
            $table->uuid('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_requests');
    }
};
