<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('schools')) {
            return;
        }

        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('scod')->nullable()->unique();
            $table->string('name');
            $table->string('education_level')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
