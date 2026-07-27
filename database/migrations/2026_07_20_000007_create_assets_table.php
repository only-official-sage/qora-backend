<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admin_id')->constrained()->onDelete('cascade');
            $table->string('type'); // logo, flyer, ig_post
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable();
            $table->timestamps();

            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
