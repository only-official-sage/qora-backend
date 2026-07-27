<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->constrained()->onDelete('cascade');
            $table->uuid('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->enum('type', ['waiter', 'cleanup', 'other'])->default('waiter');
            $table->enum('status', ['pending', 'handled', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('table_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
