<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_school_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_period_id')->constrained('ami_periods')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->string('status')->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['ami_period_id', 'school_id']);
            $table->index(['ami_period_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_school_assignments');
    }
};
