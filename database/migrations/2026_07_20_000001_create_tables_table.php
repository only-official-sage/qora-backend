<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->constrained()->onDelete('cascade');
            $table->uuid('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->string('name');
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->integer('capacity')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
