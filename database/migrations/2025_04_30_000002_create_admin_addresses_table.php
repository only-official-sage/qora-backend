<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
     Schema::create('admin_addresses', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('admin_id')->constrained()->onDelete('cascade');
    $table->text('address')->nullable();
    $table->string('country')->nullable();
    $table->string('state')->nullable();
    $table->string('city')->nullable();
    $table->string('country_code')->nullable();
    $table->string('phone_number')->nullable();
    $table->timestamps();
    $table->unique('admin_id');
});
    }

    public function down()
    {
        Schema::dropIfExists('admin_addresses');
    }
};
