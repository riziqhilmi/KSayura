<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Kantor Sayur');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->integer('check_in_radius')->default(100); // meters
            $table->time('check_in_start')->nullable();
            $table->time('check_in_end')->nullable();
            $table->enum('overtime_calculation', ['hourly', 'daily'])->default('hourly');
            $table->integer('overtime_minutes')->default(30);
            $table->time('office_start_time')->default('08:00:00');
            $table->time('office_end_time')->default('17:00:00');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_settings');
    }
};