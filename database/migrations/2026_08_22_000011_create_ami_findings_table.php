<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ami_school_assignment_id')->constrained('ami_school_assignments')->cascadeOnDelete();
            $table->foreignId('ami_indicator_id')->nullable()->constrained('ami_indicators')->nullOnDelete();
            $table->foreignId('auditor_id')->constrained('users')->restrictOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->text('recommendation');
            $table->string('status')->default('open');
            $table->timestamps();

            $table->index(['ami_school_assignment_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_findings');
    }
};
