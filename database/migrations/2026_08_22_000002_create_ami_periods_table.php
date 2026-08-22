<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ami_periods')) {
            return;
        }

        Schema::create('ami_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('submission_start_at')->nullable();
            $table->timestamp('submission_end_at')->nullable();
            $table->timestamp('review_start_at')->nullable();
            $table->timestamp('review_end_at')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_periods');
    }
};
