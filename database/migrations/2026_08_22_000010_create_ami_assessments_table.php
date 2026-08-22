<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_school_assignment_id')->constrained('ami_school_assignments')->cascadeOnDelete();
            $table->foreignId('ami_indicator_id')->constrained('ami_indicators')->cascadeOnDelete();
            $table->foreignId('auditor_id')->constrained('users')->restrictOnDelete();
            $table->string('status');
            $table->decimal('score', 8, 2)->nullable();
            $table->text('auditor_note')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();

            $table->unique(['ami_school_assignment_id', 'ami_indicator_id']);
            $table->index(['ami_school_assignment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_assessments');
    }
};
