<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id');
            $table->uuid('staff_id')->nullable();
            $table->text('table_id');
            $table->json('order');
            $table->enum('status', [0,1,2,3])->default(0);
            $table->integer('report_status')->nullable();
            $table->text('report_reason')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('set null');
            $table->index(['admin_id', 'status']);
            $table->index('table_id');
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
