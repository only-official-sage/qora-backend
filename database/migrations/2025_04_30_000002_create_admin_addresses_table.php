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
            $table->text('address');
            $table->string('country');
            $table->string('state');
            $table->string('city')->nullable();
            $table->string('country_code');
            $table->string('phone_number');
            $table->timestamps();

            $table->unique('admin_id'); // one-to-one relationship
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_addresses');
    }
};
