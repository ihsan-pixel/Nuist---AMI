<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_finding_id')->constrained('ami_findings')->cascadeOnDelete();
            $table->foreignId('ami_school_assignment_id')->constrained('ami_school_assignments')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->longText('action_plan');
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('verifier_note')->nullable();
            $table->timestamps();

            $table->unique('ami_finding_id');
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_follow_ups');
    }
};
