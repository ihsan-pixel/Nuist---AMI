<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ami_standards')) {
            return;
        }

        Schema::create('ami_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_period_id')->constrained('ami_periods')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['ami_period_id', 'sort_order']);
            $table->index(['ami_period_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_standards');
    }
};
